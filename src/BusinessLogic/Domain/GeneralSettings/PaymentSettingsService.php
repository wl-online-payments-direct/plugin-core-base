<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Stores\StoreService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentSettingsRepositoryInterface;
/**
 * The ONE reader of the store's payment settings, baseline included.
 *
 * Before this existed the admin fell back to `new PaymentSettings(...getDefaultOrderStatusMapping())`
 * while checkout fell back to a bare `new PaymentSettings()`, so on a store with nothing saved the two
 * disagreed - the value a merchant was shown was not the value checkout used. ADR-0003 decision 9
 * requires a single baseline reader, since a cascade resolved from two different baselines would put
 * the admin's greyed-out "inherited" values out of step with what checkout actually sends.
 *
 * It lives in Domain and injects the READ-ONLY Domain repository contract, not the AdminConfig one:
 * the checkout services depend on this, and a Domain dependency pointing outward at AdminConfig would
 * be a `SHARED_CORE_ARCHITECTURE` §2 violation.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class PaymentSettingsService
{
    protected PaymentSettingsRepositoryInterface $paymentSettingsRepository;
    protected StoreService $storeService;
    /**
     * @param PaymentSettingsRepositoryInterface $paymentSettingsRepository
     * @param StoreService $storeService
     */
    public function __construct(PaymentSettingsRepositoryInterface $paymentSettingsRepository, StoreService $storeService)
    {
        $this->paymentSettingsRepository = $paymentSettingsRepository;
        $this->storeService = $storeService;
    }
    /**
     * @return PaymentSettings The stored settings, or the store baseline when nothing is saved.
     *
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function getPaymentSettings(): PaymentSettings
    {
        return $this->paymentSettingsRepository->getPaymentSettings() ?? $this->getBaseline();
    }
    /**
     * `PaymentSettings`' own defaults, plus the store's default order-status mapping - which is the
     * only thing the admin baseline ever added over the checkout one.
     *
     * @return PaymentSettings
     *
     * @throws InvalidPaymentAttemptsNumberException
     */
    private function getBaseline(): PaymentSettings
    {
        return (new PaymentSettings())->withOrderStatusMapping($this->storeService->getDefaultOrderStatusMapping());
    }
}
