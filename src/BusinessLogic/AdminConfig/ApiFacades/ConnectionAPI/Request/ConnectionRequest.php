<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionMode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Credentials;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionModeException;
/**
 * Class ConnectionRequest
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Request
 */
class ConnectionRequest extends Request
{
    private string $mode;
    private ?string $testPspid;
    private ?string $testApiKey;
    private ?string $testApiSecret;
    private ?string $testWebhooksKey;
    private ?string $testWebhooksSecret;
    private ?string $livePspid;
    private ?string $liveApiKey;
    private ?string $liveApiSecret;
    private ?string $liveWebhooksKey;
    private ?string $liveWebhooksSecret;
    /**
     * @param string $mode
     * @param string|null $testPspid
     * @param string|null $testApiKey
     * @param string|null $testApiSecret
     * @param string|null $testWebhooksKey
     * @param string|null $testWebhooksSecret
     * @param string|null $livePspid
     * @param string|null $liveApiKey
     * @param string|null $liveApiSecret
     * @param string|null $liveWebhooksKey
     * @param string|null $liveWebhooksSecret
     */
    public function __construct(string $mode, ?string $testPspid, ?string $testApiKey, ?string $testApiSecret, ?string $testWebhooksKey, ?string $testWebhooksSecret, ?string $livePspid = null, ?string $liveApiKey = null, ?string $liveApiSecret = null, ?string $liveWebhooksKey = null, ?string $liveWebhooksSecret = null)
    {
        $this->mode = $mode;
        $this->testPspid = $testPspid;
        $this->testApiKey = $testApiKey;
        $this->testApiSecret = $testApiSecret;
        $this->testWebhooksKey = $testWebhooksKey;
        $this->testWebhooksSecret = $testWebhooksSecret;
        $this->livePspid = $livePspid;
        $this->liveApiKey = $liveApiKey;
        $this->liveApiSecret = $liveApiSecret;
        $this->liveWebhooksKey = $liveWebhooksKey;
        $this->liveWebhooksSecret = $liveWebhooksSecret;
    }
    /**
     * @return object|ConnectionDetails
     *
     * @throws InvalidConnectionDetailsException
     * @throws InvalidConnectionModeException
     */
    public function transformToDomainModel(): object
    {
        return new ConnectionDetails(ConnectionMode::parse($this->mode), $this->liveApiKey ? new Credentials($this->livePspid, $this->liveApiKey, $this->liveApiSecret, $this->liveWebhooksKey, $this->liveWebhooksSecret) : null, $this->testApiKey ? new Credentials($this->testPspid, $this->testApiKey, $this->testApiSecret, $this->testWebhooksKey, $this->testWebhooksSecret) : null);
    }
}
