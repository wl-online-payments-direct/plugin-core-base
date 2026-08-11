<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedTokenization\ValidTokensResponse;
/**
 * Class PaymentMethodsResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response
 */
class PaymentMethodsResponse extends Response
{
    private PaymentMethodCollection $availablePaymentMethods;
    private ?ValidTokensResponse $validTokensResponse;
    /**
     * @param PaymentMethodCollection $availablePaymentMethods
     * @param ?ValidTokensResponse $validTokensResponse
     */
    public function __construct(PaymentMethodCollection $availablePaymentMethods, ?ValidTokensResponse $validTokensResponse)
    {
        $this->availablePaymentMethods = $availablePaymentMethods;
        $this->validTokensResponse = $validTokensResponse;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            // array_values() first: the collection's internal keys are numeric-string product ids,
            // which array_map() would otherwise preserve - turning this into a JSON object whose
            // integer-like keys get re-sorted ascending on the JS side, silently discarding order.
            'availablePaymentMethods' => array_map(static function (PaymentMethod $paymentMethod) {
                return ['productId' => (string) $paymentMethod->getProductId(), 'name' => $paymentMethod->getName()->toArray()];
            }, array_values($this->availablePaymentMethods->toArray())),
        ];
    }
    /**
     * @return PaymentMethodCollection
     */
    public function getPaymentMethods(): PaymentMethodCollection
    {
        return $this->availablePaymentMethods;
    }
    public function getValidTokensResponse(): ?ValidTokensResponse
    {
        return $this->validTokensResponse;
    }
}
