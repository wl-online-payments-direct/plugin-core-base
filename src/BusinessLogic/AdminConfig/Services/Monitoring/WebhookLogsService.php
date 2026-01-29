<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Monitoring;

use DateTime;
use Exception;
use WOP\OnlinePayments\Core\Branding\Brand\ActiveBrandProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect\Repositories\DisconnectRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories\WebhookLogRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\WebhookLog;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\WebhookStatuses;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodDefaultConfigs;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\WebhookData;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentsProxyInterface;
/**
 * Class WebhookLogsService
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Monitoring
 */
class WebhookLogsService
{
    protected WebhookLogRepositoryInterface $repository;
    protected PaymentsProxyInterface $paymentsProxy;
    protected DisconnectRepositoryInterface $disconnectRepository;
    protected ActiveBrandProviderInterface $activeBrandProvider;
    /**
     * @param WebhookLogRepositoryInterface $repository
     * @param PaymentsProxyInterface $paymentsProxy
     * @param DisconnectRepositoryInterface $disconnectRepository
     * @param ActiveBrandProviderInterface $activeBrandProvider
     */
    public function __construct(WebhookLogRepositoryInterface $repository, PaymentsProxyInterface $paymentsProxy, DisconnectRepositoryInterface $disconnectRepository, ActiveBrandProviderInterface $activeBrandProvider)
    {
        $this->repository = $repository;
        $this->paymentsProxy = $paymentsProxy;
        $this->disconnectRepository = $disconnectRepository;
        $this->activeBrandProvider = $activeBrandProvider;
    }
    /**
     * @param WebhookData $webhookData
     *
     * @return void
     *
     * @throws Exception
     */
    public function logWebhook(WebhookData $webhookData): void
    {
        $webhookPaymentId = PaymentId::parse($webhookData->getId());
        $payment = $this->paymentsProxy->tryToGetPayment($webhookPaymentId);
        if (!$payment) {
            // Default to first payment transaction (_0) if payment id from webhook is maintenance transaction
            $payment = $this->paymentsProxy->tryToGetPayment(PaymentId::parse($webhookPaymentId->getTransactionId()));
        }
        $paymentMethodName = '';
        if ($payment && $payment->getProductId()) {
            $paymentMethodName = PaymentMethodDefaultConfigs::getName($payment->getProductId(), $this->activeBrandProvider->getActiveBrand()->getPaymentMethodName())['translation'] ?? '';
        }
        $webhookLog = new WebhookLog($webhookData->getMerchantReference(), $webhookData->getId(), $paymentMethodName, WebhookStatuses::statusMap[$webhookData->getStatusCategory()], $webhookData->getType(), new DateTime($webhookData->getCreated()), $webhookData->getStatusCode(), $webhookData->getWebhookBody(), $this->activeBrandProvider->getTransactionUrl() . PaymentId::parse((string) $webhookData->getId())->getTransactionId());
        $this->repository->saveWebhookLog($webhookLog);
    }
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $searchTerm
     *
     * @return array
     *
     * @throws Exception
     */
    public function getLogs(int $pageNumber, int $pageSize, string $searchTerm): array
    {
        $disconnectTime = $this->disconnectRepository->getDisconnectTime();
        return $this->repository->getWebhookLogs($pageNumber, $pageSize, $searchTerm, $disconnectTime);
    }
    /**
     * @return array
     */
    public function getAllLogs(): array
    {
        $logs = $this->repository->getAllLogs();
        $result = [];
        foreach ($logs as $log) {
            $result[] = $log->toArray();
        }
        return $result;
    }
    /**
     * @param string $searchTerm
     * @return int
     *
     * @throws Exception
     */
    public function count(string $searchTerm = ''): int
    {
        $disconnectTime = $this->disconnectRepository->getDisconnectTime();
        return $this->repository->count($disconnectTime, $searchTerm);
    }
    /**
     * @param string $mode
     * @param int $limit
     *
     * @return void
     *
     * @throws Exception
     */
    public function delete(string $mode, int $limit = 5000): void
    {
        $disconnectTime = $this->disconnectRepository->getDisconnectTime();
        $this->repository->deleteByMode($disconnectTime, $mode, $limit);
    }
}
