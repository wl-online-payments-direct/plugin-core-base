<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\CheckoutAPI\CheckoutAPI;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\Runnable;
/**
 * Class WaitPaymentOutcomeProcessRunner.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class WaitPaymentOutcomeProcessRunner implements Runnable
{
    private PaymentId $paymentId;
    private ?string $returnHmac;
    private ?string $merchantReference;
    private string $storeId;
    private string $tenantId;
    public function __construct(PaymentId $paymentId, ?string $returnHmac, ?string $merchantReference, string $storeId, string $tenantId = '')
    {
        $this->paymentId = $paymentId;
        $this->returnHmac = $returnHmac;
        $this->merchantReference = $merchantReference;
        $this->storeId = $storeId;
        $this->tenantId = $tenantId;
    }
    public function run(): void
    {
        TenantContext::doWithTenant($this->tenantId, function () {
            CheckoutAPI::get()->forTenant($this->tenantId)->payment($this->storeId)->startWaitingForOutcome($this->paymentId, $this->returnHmac, $this->merchantReference);
        });
    }
    public static function fromArray(array $array): Serializable
    {
        return new WaitPaymentOutcomeProcessRunner(PaymentId::parse($array['paymentId']), $array['returnHmac'], $array['merchantReference'], $array['storeId'], $array['tenantId'] ?? '');
    }
    public function toArray(): array
    {
        return ['paymentId' => (string) $this->paymentId, 'returnHmac' => $this->returnHmac, 'merchantReference' => $this->merchantReference, 'storeId' => $this->storeId, 'tenantId' => $this->tenantId];
    }
}
