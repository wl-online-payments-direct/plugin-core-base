<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect\Repositories;

use DateTime;
use Exception;
/**
 * Interface DisconnectRepositoryInterface
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect\Repositories
 */
interface DisconnectRepositoryInterface
{
    /**
     * Retrieves disconnect time.
     *
     * @return DateTime|null
     *
     * @throws Exception
     */
    public function getDisconnectTime(): ?DateTime;
    /**
     * Sets disconnect time.
     *
     * @param DateTime $disconnectTime
     *
     * @return void
     *
     * @throws Exception
     */
    public function setDisconnectTime(DateTime $disconnectTime): void;
    /**
     * Deletes disconnect time.
     *
     * @return void
     *
     * @throws Exception
     */
    public function deleteDisconnectTime(): void;
}
