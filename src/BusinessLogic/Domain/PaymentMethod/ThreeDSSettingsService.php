<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Repositories\PaymentConfigRepositoryInterface;
/**
 * Class ThreeDSSettingsService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class ThreeDSSettingsService
{
    protected PaymentConfigRepositoryInterface $paymentConfigRepository;
    /**
     * @param PaymentConfigRepositoryInterface $paymentConfigRepository
     */
    public function __construct(PaymentConfigRepositoryInterface $paymentConfigRepository)
    {
        $this->paymentConfigRepository = $paymentConfigRepository;
    }
    /**
     * @param PaymentProductId $paymentProductId
     *
     * @return ThreeDSSettings|null
     */
    public function getThreeDSSettings(PaymentProductId $paymentProductId): ?ThreeDSSettings
    {
        if (!PaymentProductId::googlePay()->equals($paymentProductId->getId()) && !PaymentProductId::hostedCheckout()->equals($paymentProductId->getId()) && !$paymentProductId->isCardType()) {
            return null;
        }
        if ($paymentProductId->isCardType()) {
            $config = $this->paymentConfigRepository->getPaymentMethod(PaymentProductId::cards());
            return $config ? $config->getAdditionalData()->getThreeDSSettings() : null;
        }
        $config = $this->paymentConfigRepository->getPaymentMethod($paymentProductId->getId());
        return $config && $config->getAdditionalData() ? $config->getAdditionalData()->getThreeDSSettings() : null;
    }
}
