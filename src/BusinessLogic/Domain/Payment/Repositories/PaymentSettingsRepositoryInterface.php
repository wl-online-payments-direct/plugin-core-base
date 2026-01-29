<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
/**
 * Interface PaymentSettingsRepositoryInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories
 */
interface PaymentSettingsRepositoryInterface
{
    public function getPaymentSettings(): ?PaymentSettings;
}
