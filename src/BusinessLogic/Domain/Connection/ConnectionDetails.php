<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class ConnectionDetails.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Connection
 */
class ConnectionDetails
{
    private ConnectionMode $mode;
    private ?Credentials $liveCredentials;
    private ?Credentials $testCredentials;
    /**
     * @throws InvalidConnectionDetailsException
     */
    public function __construct(ConnectionMode $mode, ?Credentials $liveCredentials = null, ?Credentials $testCredentials = null)
    {
        $this->mode = $mode;
        if ($this->mode->equals(ConnectionMode::live()) && null === $liveCredentials) {
            throw new InvalidConnectionDetailsException(new TranslatableLabel('Connection details are invalid. Missing live credentials.', 'connection.invalidLiveCredentials'));
        }
        if ($this->mode->equals(ConnectionMode::test()) && null === $testCredentials) {
            throw new InvalidConnectionDetailsException(new TranslatableLabel('Connection details are invalid. Missing test credentials.', 'connection.invalidTestCredentials'));
        }
        $this->liveCredentials = $liveCredentials;
        $this->testCredentials = $testCredentials;
    }
    /**
     * @return ConnectionMode
     */
    public function getMode(): ConnectionMode
    {
        return $this->mode;
    }
    public function getActiveCredentials(): Credentials
    {
        return ConnectionMode::live()->equals($this->mode) ? $this->liveCredentials : $this->testCredentials;
    }
    public function getLiveCredentials(): ?Credentials
    {
        return $this->liveCredentials;
    }
    public function getTestCredentials(): ?Credentials
    {
        return $this->testCredentials;
    }
}
