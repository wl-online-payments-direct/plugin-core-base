<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Sdk;

use WOP\OnlinePayments\Core\Branding\Brand\ActiveBrandProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionMode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Metadata\MetadataProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use OnlinePayments\Sdk\Authentication\V1HmacAuthenticator;
use OnlinePayments\Sdk\Client;
use OnlinePayments\Sdk\CommunicatorConfiguration;
use OnlinePayments\Sdk\Merchant\MerchantClientInterface;
/**
 * Class MerchantClientFactory.
 *
 * @package OnlinePayments\Core\Bootstrap\Sdk
 */
class MerchantClientFactory
{
    private const INTEGRATOR = 'Logeecom';
    private ActiveConnectionProvider $activeConnectionProvider;
    private ActiveBrandProviderInterface $activeBrandProvider;
    private MetadataProviderInterface $metadataProvider;
    public function __construct(ActiveConnectionProvider $activeConnectionProvider, ActiveBrandProviderInterface $activeBrandProvider, MetadataProviderInterface $metadataProvider)
    {
        $this->activeConnectionProvider = $activeConnectionProvider;
        $this->activeBrandProvider = $activeBrandProvider;
        $this->metadataProvider = $metadataProvider;
    }
    /**
     * @param ConnectionDetails|null $activeConnection
     * @param int|null $timeoutSeconds Per-request connect and read timeout. Null leaves the SDK default
     *  (no timeout) in place.
     *
     * @return MerchantClientInterface
     */
    public function get(?ConnectionDetails $activeConnection = null, ?int $timeoutSeconds = null): MerchantClientInterface
    {
        if (null === $activeConnection) {
            $activeConnection = $this->activeConnectionProvider->get();
        }
        if (null === $activeConnection) {
            throw new InvalidConnectionDetailsException(new TranslatableLabel('Connection details are invalid. Missing active credentials.', 'connection.invalidActiveCredentials'));
        }
        $communicatorConfiguration = new CommunicatorConfiguration($activeConnection->getActiveCredentials()->getApiKey(), $activeConnection->getActiveCredentials()->getApiSecret(), $this->getApiEndpoint($activeConnection), self::INTEGRATOR);
        if (null !== $timeoutSeconds) {
            $communicatorConfiguration->setConnectTimeout($timeoutSeconds);
            $communicatorConfiguration->setReadTimeout($timeoutSeconds);
        }
        // DefaultConnection reads the timeouts once, in its constructor, so the configuration has to be
        // complete before the connection is built.
        $dbLogConnection = new DbLogConnection(new CommunicatorLoggerHelper(), $communicatorConfiguration);
        $dbLogConnection->enableLogging(new ApiLogger());
        $authenticator = new V1HmacAuthenticator($communicatorConfiguration);
        $communicator = new MetricsProvidingCommunicator($communicatorConfiguration, $authenticator, $dbLogConnection, $this->activeBrandProvider, $this->metadataProvider, StoreContext::getInstance());
        $client = new Client($communicator);
        return $client->merchant($activeConnection->getActiveCredentials()->getPspId());
    }
    private function getApiEndpoint(ConnectionDetails $activeConnection): string
    {
        $brandConfig = $this->activeBrandProvider->getActiveBrand();
        if ($activeConnection->getMode()->equals(ConnectionMode::live())) {
            return $brandConfig->getLiveApiEndpoint();
        }
        return $brandConfig->getTestApiEndpoint();
    }
}
