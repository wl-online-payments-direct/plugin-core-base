<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

/**
 * Class StatusOutput.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class StatusOutput
{
    /**
     * @var StatusError[]
     */
    private array $errors;
    /**
     * @var bool
     */
    private ?bool $authorized;
    /**
     * @var bool
     */
    private ?bool $cancellable;
    /**
     * @var bool
     */
    private ?bool $refundable;
    /**
     * @param bool|null $isAuthorized
     * @param bool|null $isCancellable
     * @param bool|null $isRefundable
     * @param StatusError[] $errors
     */
    public function __construct(?bool $isAuthorized, ?bool $isCancellable, ?bool $isRefundable, array $errors = [])
    {
        $this->errors = $errors;
        $this->authorized = $isAuthorized;
        $this->cancellable = $isCancellable;
        $this->refundable = $isRefundable;
    }
    public function getErrors(): array
    {
        return $this->errors;
    }
    public function isAuthorized(): ?bool
    {
        return $this->authorized;
    }
    public function isCancellable(): ?bool
    {
        return $this->cancellable;
    }
    public function isRefundable(): ?bool
    {
        return $this->refundable;
    }
}
