<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodResponse;
/**
 * Class PaymentMethodsResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response
 */
class PaymentMethodsResponse extends Response
{
    /**
     * @var PaymentMethodResponse[]
     */
    private array $paymentMethods;
    /**
     * @param array $paymentMethods
     */
    public function __construct(array $paymentMethods)
    {
        $this->paymentMethods = $paymentMethods;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->paymentMethods as $paymentMethod) {
            $result[] = [
                'paymentProductId' => $paymentMethod->getPaymentProductId(),
                'name' => $paymentMethod->getName()->getDefaultTranslation()->toArray(),
                'paymentGroup' => $paymentMethod->getPaymentGroup(),
                'integrationTypes' => $paymentMethod->getIntegrationTypes(),
                'enabled' => $paymentMethod->isEnabled(),
                // The flow this method is CONFIGURED as, where that is a setting at all; null
                // otherwise. The admin list labels a card brand by this rather than by
                // `integrationTypes`, which lists the flows the product supports - a brand supports
                // several and runs as one, so the capability list cannot name the row.
                'flowType' => $paymentMethod->getFlowType(),
            ];
        }
        return $result;
    }
}
