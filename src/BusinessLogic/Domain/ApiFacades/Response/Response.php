<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response;

/**
 * Class Response
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response
 */
abstract class Response
{
    /**
     * @var bool
     */
    protected bool $successful = \true;
    /**
     * @var int
     */
    protected int $statusCode = 200;
    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    /**
     * @return array
     */
    abstract public function toArray(): array;
}
