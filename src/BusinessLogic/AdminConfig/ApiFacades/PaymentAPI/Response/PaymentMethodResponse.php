<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\ThreeDSSettingsSerializer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\BankTransfer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Descriptor;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\GooglePay;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\HostedCheckout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Oney;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PayByLink;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\ResolvedThreeDSSettings;
/**
 * Class PaymentMethodResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response
 */
class PaymentMethodResponse extends Response
{
    private PaymentMethod $paymentMethod;
    private ?ResolvedThreeDSSettings $resolvedThreeDSSettings;
    /**
     * @param PaymentMethod $paymentMethod
     * @param ResolvedThreeDSSettings|null $resolvedThreeDSSettings What the method's 3DS block
     *     resolves to today, and which level answered. Omitted where the caller has no resolver -
     *     listings, which do not render the inheritance state.
     */
    public function __construct(PaymentMethod $paymentMethod, ?ResolvedThreeDSSettings $resolvedThreeDSSettings = null)
    {
        $this->paymentMethod = $paymentMethod;
        $this->resolvedThreeDSSettings = $resolvedThreeDSSettings;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'paymentProductId' => (string) $this->paymentMethod->getProductId(),
            'name' => $this->paymentMethod->getName()->toArray(),
            'enabled' => $this->paymentMethod->isEnabled(),
            'template' => $this->paymentMethod->getTemplate(),
            'additionalData' => $this->additionalDataToArray(),
            'paymentAction' => $this->paymentMethod->getPaymentAction() ? $this->paymentMethod->getPaymentAction()->getType() : '',
            // Empty means this method inherits the store's locale, which is what the modal renders as
            // the greyed-out placeholder. Deliberately NOT resolved into the store value here - doing
            // that would erase the distinction and make an inheriting method look configured.
            'fallbackLocale' => $this->paymentMethod->getFallbackLocale(),
            // Beside `additionalData`, not inside it. `additionalData` is what the merchant SAVED;
            // this is what a payment would be sent with right now, which is a different question and
            // for most methods a different answer.
            'resolvedThreeDSSettings' => $this->resolvedThreeDSSettingsToArray(),
        ];
    }
    /**
     * The resolved block plus its source, so the admin can grey out inherited fields.
     *
     * ONE source for the whole block rather than one per field: the block resolves as a unit
     * (ADR-0003 decision 5), so all five fields always share a source. Decision 10 sketched a
     * per-field shape, which would be more granular than the model can express and would invite the UI
     * to render mixed states that cannot occur.
     */
    private function resolvedThreeDSSettingsToArray(): ?array
    {
        if (null === $this->resolvedThreeDSSettings) {
            return null;
        }
        return array_merge(ThreeDSSettingsSerializer::toArray($this->resolvedThreeDSSettings->getSettings()), ['source' => $this->resolvedThreeDSSettings->getSource(), 'inherited' => $this->resolvedThreeDSSettings->isInherited()]);
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
        // The grouped `cards` method and every individual card brand expose the same block, so the
        // admin UI needs one renderer for all of them (ADR-0002).
        if ($this->paymentMethod->getProductId()->hasCreditCardConfiguration()) {
            /** @var CreditCard $additionalData */
            $vaultTitles = $additionalData->getVaultTitles();
            return array_merge(['vaultTitleCollection' => $vaultTitles ? $vaultTitles->toArray() : [], 'enableGroupCards' => $additionalData->isEnableGroupCards()], ThreeDSSettingsSerializer::toArray($additionalData->getThreeDSSettings()), [
                'flowType' => $additionalData->getType()->getType(),
                'authorizationMode' => $additionalData->getAuthorizationMode()->getType(),
                // Sent for every card method so the admin has one shape to render, but only the
                // two parents show the control - an individual brand offers exactly itself.
                'allowedBrands' => $additionalData->getAllowedBrands(),
                // Only brands the merchant actually overrode appear. An absent brand is what the
                // admin renders greyed out as inherited, so filling this in for every allowed
                // brand would erase exactly the distinction the modal has to show.
                'brandThreeDSOverrides' => ThreeDSSettingsSerializer::mapToArray($additionalData->getBrandThreeDSOverrides()),
            ]);
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::hostedCheckout()->getId())) {
            /** @var HostedCheckout $additionalData */
            return array_merge(['logo' => $additionalData->getLogo(), 'enableGroupCards' => $additionalData->isEnableGroupCards()], ThreeDSSettingsSerializer::toArray($additionalData->getThreeDSSettings()));
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
            return ThreeDSSettingsSerializer::toArray($additionalData->getThreeDSSettings());
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::payByLink()->getId())) {
            /** @var PayByLink $additionalData */
            return array_merge(['expirationTime' => $additionalData->getExpirationTime()->getDays(), 'enableGroupCards' => $additionalData->isEnableGroupCards()], ThreeDSSettingsSerializer::toArray($additionalData->getThreeDSSettings()));
        }
        if ($this->paymentMethod->getProductId()->isDescriptorSupported()) {
            /** @var Descriptor $additionalData */
            return ['descriptor' => $additionalData->getDescriptor()];
        }
        return [];
    }
}
