<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection\Proxies\ConnectionProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\Proxies\HealthCheckProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use Throwable;
/**
 * Class HealthCheckService
 *
 * Runs the three connection/webhook validation checks - Payment API test connection, webhook credential
 * validation, and a mock webhook send - in sequence, stopping at the first step that does not pass.
 * Reusable by both the "Save and Check Credentials" and "Check Credentials only" (no save) admin flows,
 * so it takes no position on whether/what to persist - callers decide that from the returned
 * HealthCheckResult.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck
 */
class HealthCheckService
{
    /**
     * Origin reported in the `platforminfo` header of every request this service makes.
     */
    private const ORIGIN = 'accountConfig';
    /**
     * Retries granted to a single check after a technical failure, and the pause between them.
     */
    private const MAX_RETRIES = 2;
    private const RETRY_DELAY_SECONDS = 3;
    private const MAX_ATTEMPTS = self::MAX_RETRIES + 1;
    /**
     * Per-request timeout, in seconds, that the proxies backing these checks apply.
     */
    public const REQUEST_TIMEOUT_SECONDS = 11;
    private ConnectionProxyInterface $connectionProxy;
    private HealthCheckProxyInterface $healthCheckProxy;
    private ApiFailureClassifierInterface $failureClassifier;
    private TimeProviderInterface $timeProvider;
    public function __construct(ConnectionProxyInterface $connectionProxy, HealthCheckProxyInterface $healthCheckProxy, ApiFailureClassifierInterface $failureClassifier, TimeProviderInterface $timeProvider)
    {
        $this->connectionProxy = $connectionProxy;
        $this->healthCheckProxy = $healthCheckProxy;
        $this->failureClassifier = $failureClassifier;
        $this->timeProvider = $timeProvider;
    }
    /**
     * @param ConnectionDetails $connectionDetails Credentials being checked - not necessarily the
     *  already-persisted ones, since this also backs the "check before saving" flow.
     * @param string $webhookUrl URL the mock webhook event should be sent to for the sendtest step.
     *
     * @return HealthCheckResult
     */
    public function check(ConnectionDetails $connectionDetails, string $webhookUrl): HealthCheckResult
    {
        $steps = [];
        $testConnection = $this->runStep($steps, HealthCheckStep::testConnection(), function () use ($connectionDetails) {
            return $this->connectionProxy->isConnectionValid($connectionDetails);
        });
        if (!$testConnection->isPassed()) {
            $this->skip($steps, [HealthCheckStep::validateCredentials(), HealthCheckStep::sendTest()]);
            return new HealthCheckResult($steps);
        }
        $credentials = $this->runStep($steps, HealthCheckStep::validateCredentials(), function () use ($connectionDetails) {
            return $this->healthCheckProxy->isWebhookCredentialsValid($connectionDetails);
        });
        if (!$credentials->isPassed()) {
            $this->skip($steps, [HealthCheckStep::sendTest()]);
            return new HealthCheckResult($steps);
        }
        $steps[] = new HealthCheckStepResult(HealthCheckStep::sendTest(), $this->sendTestWebhook($connectionDetails, $webhookUrl));
        return new HealthCheckResult($steps);
    }
    /**
     * Sends one mock webhook event and reports how it went, under the same retry policy as every other
     * check (see attempt()): a KO answer from Worldline is definitive, a technical failure is retried
     * twice before the step is reported as a technical error.
     *
     * This is the ONE place a test webhook is sent. check() runs it as its third step, and the
     * standalone "Send Test Webhook" admin action (WBS S1) calls it per configured URL - neither has
     * its own copy of the send or of the retry policy.
     *
     * A passed outcome means Worldline accepted the send request, not that the endpoint received the
     * event; nothing in this API reports the latter.
     *
     * @param ConnectionDetails $connectionDetails
     * @param string $webhookUrl
     *
     * @return HealthCheckStepOutcome
     */
    public function sendTestWebhook(ConnectionDetails $connectionDetails, string $webhookUrl): HealthCheckStepOutcome
    {
        return $this->attempt(function () use ($connectionDetails, $webhookUrl) {
            $this->healthCheckProxy->sendTestWebhook($connectionDetails, $webhookUrl);
            return \true;
        });
    }
    /**
     * Runs one step, appends its result to $steps, and reports the outcome.
     *
     * @param HealthCheckStepResult[] $steps
     * @param HealthCheckStep $step
     * @param callable(): bool $operation
     *
     * @return HealthCheckStepOutcome
     */
    private function runStep(array &$steps, HealthCheckStep $step, callable $operation): HealthCheckStepOutcome
    {
        $outcome = $this->attempt($operation);
        $steps[] = new HealthCheckStepResult($step, $outcome);
        return $outcome;
    }
    /**
     * Runs $operation, retrying only technical failures - network problems, timeouts, platform (5xx)
     * errors - up to self::MAX_RETRIES times with a fixed pause in between. A known negative (Worldline
     * answered "no": bad credentials, inactive account) is definitive and ends the step immediately.
     *
     * Every attempt is a full, separate API call, so the SDK transport writes its own request/response
     * pair to the debug log for each one, retries and known negatives included.
     *
     * @param callable(): bool $operation
     *
     * @return HealthCheckStepOutcome
     */
    private function attempt(callable $operation): HealthCheckStepOutcome
    {
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            // setOrigin() is first-write-wins, so it needs a fresh reset right before each call;
            // MetricsProvidingCommunicator resets it again after building that call's headers.
            StoreContext::getInstance()->resetOrigin();
            StoreContext::getInstance()->setOrigin(self::ORIGIN);
            try {
                return $operation() ? HealthCheckStepOutcome::passed() : HealthCheckStepOutcome::failed();
            } catch (Throwable $e) {
                if ($this->failureClassifier->isKnownNegative($e)) {
                    return HealthCheckStepOutcome::failed();
                }
                if ($attempt < self::MAX_ATTEMPTS) {
                    $this->timeProvider->sleep(self::RETRY_DELAY_SECONDS);
                }
            }
        }
        return HealthCheckStepOutcome::technicalError();
    }
    /**
     * @param HealthCheckStepResult[] $steps
     * @param HealthCheckStep[] $remainingSteps
     */
    private function skip(array &$steps, array $remainingSteps): void
    {
        foreach ($remainingSteps as $step) {
            $steps[] = new HealthCheckStepResult($step, HealthCheckStepOutcome::skipped());
        }
    }
}
