<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

/**
 * Class StatusError.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class StatusError
{
    private ?string $errorCode;
    private ?string $id;
    /**
     * @param string|null $errorCode
     * @param string|null $id
     */
    public function __construct(?string $errorCode, ?string $id)
    {
        $this->errorCode = $errorCode;
        $this->id = $id;
    }
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
    public function getId(): ?string
    {
        return $this->id;
    }
}
