<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Connection;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionMode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Credentials;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionModeException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class PaymentMethodConfigEntity.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentMethod
 */
class ConnectionConfigEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected ConnectionDetails $connectionDetails;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        return new EntityConfiguration($indexMap, 'ConnectionConfig');
    }
    /**
     * @inheritDoc
     *
     * @throws InvalidConnectionDetailsException
     * @throws InvalidConnectionModeException
     */
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $connectionDetails = $data['connectionDetails'] ?? [];
        $liveCredentials = null;
        $testCredentials = null;
        if (!empty($connectionDetails['liveCredentials'])) {
            $liveCredentials = new Credentials($connectionDetails['liveCredentials']['pspId'], $connectionDetails['liveCredentials']['apiKey'], $connectionDetails['liveCredentials']['apiSecret'], $connectionDetails['liveCredentials']['webhookKey'], $connectionDetails['liveCredentials']['webhookSecret']);
        }
        if (!empty($connectionDetails['testCredentials'])) {
            $testCredentials = new Credentials($connectionDetails['testCredentials']['pspId'], $connectionDetails['testCredentials']['apiKey'], $connectionDetails['testCredentials']['apiSecret'], $connectionDetails['testCredentials']['webhookKey'], $connectionDetails['testCredentials']['webhookSecret']);
        }
        $this->connectionDetails = new ConnectionDetails(ConnectionMode::parse($connectionDetails['mode']), $liveCredentials, $testCredentials);
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['connectionDetails'] = ['mode' => (string) $this->connectionDetails->getMode()];
        if ($this->connectionDetails->getLiveCredentials()) {
            $data['connectionDetails']['liveCredentials'] = ['pspId' => $this->connectionDetails->getLiveCredentials()->getPspId(), 'apiKey' => $this->connectionDetails->getLiveCredentials()->getApiKey(), 'apiSecret' => $this->connectionDetails->getLiveCredentials()->getApiSecret(), 'webhookKey' => $this->connectionDetails->getLiveCredentials()->getWebhookKey(), 'webhookSecret' => $this->connectionDetails->getLiveCredentials()->getWebhookSecret()];
        }
        if ($this->connectionDetails->getTestCredentials()) {
            $data['connectionDetails']['testCredentials'] = ['pspId' => $this->connectionDetails->getTestCredentials()->getPspId(), 'apiKey' => $this->connectionDetails->getTestCredentials()->getApiKey(), 'apiSecret' => $this->connectionDetails->getTestCredentials()->getApiSecret(), 'webhookKey' => $this->connectionDetails->getTestCredentials()->getWebhookKey(), 'webhookSecret' => $this->connectionDetails->getTestCredentials()->getWebhookSecret()];
        }
        return $data;
    }
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    public function getConnectionDetails(): ConnectionDetails
    {
        return $this->connectionDetails;
    }
    public function setConnectionDetails(ConnectionDetails $connectionDetails): void
    {
        $this->connectionDetails = $connectionDetails;
    }
}
