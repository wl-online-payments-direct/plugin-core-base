<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkSettings;
/**
 * Interface PayByLinkSettingsRepositoryInterface
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories
 */
interface PayByLinkSettingsRepositoryInterface
{
    /**
     * @return PayByLinkSettings|null
     */
    public function getPayByLinkSettings(): ?PayByLinkSettings;
    /**
     * @param PayByLinkSettings $payByLinkSettings
     *
     * @return void
     */
    public function savePayByLinkSettings(PayByLinkSettings $payByLinkSettings): void;
    /**
     * @param string $mode
     *
     * @return void
     */
    public function deleteByMode(string $mode): void;
}
