<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
/**
 * Class ConnectionConfigResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Response
 */
class ConnectionConfigResponse extends Response
{
    private ?ConnectionDetails $connection;
    /**
     * @param ConnectionDetails|null $connection
     */
    public function __construct(?ConnectionDetails $connection)
    {
        $this->connection = $connection;
    }
    public function toArray(): array
    {
        if (!$this->connection) {
            return [];
        }
        return ['mode' => (string) $this->connection->getMode(), 'sandboxData' => ['pspid' => $this->connection->getTestCredentials() ? $this->connection->getTestCredentials()->getPspid() : '', 'apiKey' => $this->connection->getTestCredentials() ? $this->connection->getTestCredentials()->getApiKey() : '', 'apiSecret' => $this->connection->getTestCredentials() ? $this->connection->getTestCredentials()->getApiSecret() : '', 'webhooksKey' => $this->connection->getTestCredentials() ? $this->connection->getTestCredentials()->getWebhookKey() : '', 'webhooksSecret' => $this->connection->getTestCredentials() ? $this->connection->getTestCredentials()->getWebhookSecret() : ''], 'liveData' => ['pspid' => $this->connection->getLiveCredentials() ? $this->connection->getLiveCredentials()->getPspid() : '', 'apiKey' => $this->connection->getLiveCredentials() ? $this->connection->getLiveCredentials()->getApiKey() : '', 'apiSecret' => $this->connection->getLiveCredentials() ? $this->connection->getLiveCredentials()->getApiSecret() : '', 'webhooksKey' => $this->connection->getLiveCredentials() ? $this->connection->getLiveCredentials()->getWebhookKey() : null, 'webhooksSecret' => $this->connection->getLiveCredentials() ? $this->connection->getLiveCredentials()->getWebhookSecret() : null]];
    }
}
