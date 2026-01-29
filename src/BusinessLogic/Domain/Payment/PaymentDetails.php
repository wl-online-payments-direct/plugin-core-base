<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
/**
 * Class PaymentDetails.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class PaymentDetails
{
    private StatusCode $statusCode;
    private Amount $amount;
    private ?string $tokenId;
    private ?PaymentAmounts $amounts;
    private bool $fullyPaid;
    private string $paymentMethod;
    private ?StatusOutput $statusOutput;
    private ?PaymentSpecificOutput $paymentSpecificOutput;
    private ?string $status;
    /** @var PaymentOperation[] */
    private array $operations;
    public function __construct(StatusCode $statusCode, Amount $amount, ?string $tokenId, ?PaymentAmounts $amounts = null, bool $fullyPaid = \false, string $paymentMethod = '', ?StatusOutput $statusOutput = null, ?PaymentSpecificOutput $paymentSpecificOutput = null, ?string $status = null, $operations = [])
    {
        $this->statusCode = $statusCode;
        $this->amount = $amount;
        $this->tokenId = $tokenId;
        $this->amounts = $amounts;
        $this->fullyPaid = $fullyPaid;
        $this->paymentMethod = $paymentMethod;
        $this->statusOutput = $statusOutput;
        $this->paymentSpecificOutput = $paymentSpecificOutput;
        $this->status = $status;
        $this->operations = $operations;
    }
    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getTokenId(): ?string
    {
        return $this->tokenId;
    }
    public function getAmounts(): ?PaymentAmounts
    {
        return $this->amounts;
    }
    public function isFullyPaid(): bool
    {
        return $this->fullyPaid;
    }
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
    public function getStatusOutput(): ?StatusOutput
    {
        return $this->statusOutput;
    }
    public function getPaymentSpecificOutput(): ?PaymentSpecificOutput
    {
        return $this->paymentSpecificOutput;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function getOperations(): array
    {
        return $this->operations;
    }
}
