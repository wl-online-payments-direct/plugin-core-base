<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\BankTransfer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\GooglePay;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\HostedCheckout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Oney;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
/**
 * Class PaymentMethodResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response
 */
class PaymentMethodResponse extends Response
{
    private PaymentMethod $paymentMethod;
    /**
     * @param PaymentMethod $paymentMethod
     */
    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['paymentProductId' => (string) $this->paymentMethod->getProductId(), 'name' => $this->paymentMethod->getName()->toArray(), 'enabled' => $this->paymentMethod->isEnabled(), 'template' => $this->paymentMethod->getTemplate(), 'additionalData' => $this->additionalDataToArray(), 'paymentAction' => $this->paymentMethod->getPaymentAction() ? $this->paymentMethod->getPaymentAction()->getType() : ''];
    }
    /**
     * @return array
     */
    protected function additionalDataToArray(): array
    {
        $additionalData = $this->paymentMethod->getAdditionalData() ?? [];
        if (!$additionalData) {
            return [];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::bankTransfer()->getId())) {
            /** @var BankTransfer $additionalData */
            return ['instantPayment' => $additionalData->isInstantPayment()];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::cards()->getId())) {
            /** @var CreditCard $additionalData */
            return ['vaultTitleCollection' => $additionalData->getVaultTitles()->toArray(), 'enableGroupCards' => $additionalData->isEnableGroupCards(), 'enable3ds' => $additionalData->getThreeDSSettings()->isEnable3ds(), 'enforceStrongAuthentication' => $additionalData->getThreeDSSettings()->isEnforceStrongAuthentication(), 'enable3dsExemption' => $additionalData->getThreeDSSettings()->isEnable3dsExemption(), 'exemptionType' => $additionalData->getThreeDSSettings()->getExemptionType()->getType(), 'exemptionLimit' => $additionalData->getThreeDSSettings()->getExemptionLimit()->getPriceInCurrencyUnits(), 'flowType' => $additionalData->getType()->getType()];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::hostedCheckout()->getId())) {
            /** @var HostedCheckout $additionalData */
            return ['logo' => $additionalData->getLogo(), 'enableGroupCards' => $additionalData->isEnableGroupCards(), 'enable3ds' => $additionalData->getThreeDSSettings()->isEnable3ds(), 'enforceStrongAuthentication' => $additionalData->getThreeDSSettings()->isEnforceStrongAuthentication(), 'enable3dsExemption' => $additionalData->getThreeDSSettings()->isEnable3dsExemption(), 'exemptionType' => $additionalData->getThreeDSSettings()->getExemptionType()->getType(), 'exemptionLimit' => $additionalData->getThreeDSSettings()->getExemptionLimit()->getPriceInCurrencyUnits()];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::intersolve()->getId())) {
            /** @var Intersolve $additionalData */
            return ['sessionTimeout' => $additionalData->getSessionTimeout()->getDuration(), 'paymentProductId' => $additionalData->getProductId() ? $additionalData->getProductId()->getId() : null];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::oney3x()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oney4x()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oneyBankCard()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oneyFinancementLong()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oneyBrandedGiftCard()->getId())) {
            /** @var Oney $additionalData */
            return ['paymentOption' => $additionalData->getPaymentOption()];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::sepaDirectDebit()->getId())) {
            /** @var Sepa $additionalData */
            return ['recurrenceType' => $additionalData->getRecurrenceType()->getType(), 'signatureType' => $additionalData->getSignatureType()->getType()];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::googlePay()->getId())) {
            /** @var GooglePay $additionalData */
            return ['enable3ds' => $additionalData->getThreeDSSettings()->isEnable3ds(), 'enforceStrongAuthentication' => $additionalData->getThreeDSSettings()->isEnforceStrongAuthentication(), 'enable3dsExemption' => $additionalData->getThreeDSSettings()->isEnable3dsExemption(), 'exemptionType' => $additionalData->getThreeDSSettings()->getExemptionType()->getType(), 'exemptionLimit' => $additionalData->getThreeDSSettings()->getExemptionLimit()->getPriceInCurrencyUnits()];
        }
        return [];
    }
}
