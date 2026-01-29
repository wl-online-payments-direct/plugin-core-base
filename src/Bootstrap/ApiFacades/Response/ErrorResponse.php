<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Response;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Aspects\ErrorHandlingAspect;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use Throwable;
/**
 * Class ErrorResponse
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Response
 */
class ErrorResponse extends Response
{
    /**
     * @inheritdoc
     */
    protected bool $successful = \false;
    /**
     * @var int
     */
    protected int $statusCode = 400;
    /**
     * @var Throwable
     */
    protected Throwable $error;
    /**
     * @param Throwable $error
     */
    protected function __construct(Throwable $error)
    {
        $this->error = $error;
        $this->statusCode = $error->getCode() > 0 ? $error->getCode() : 400;
    }
    /**
     * Implementation is swallowing all undefined calls to avoid undefined method call exceptions when
     * @see ErrorHandlingAspect already hanled the API call exception but because of chaining calle will trigger
     * API controller messages on instance of the @see self.
     *
     * @param $methodName
     * @param $arguments
     *
     * @return self Already handled error response
     */
    public function __call($methodName, $arguments)
    {
        return $this;
    }
    /**
     * @param Throwable $e
     *
     * @return self
     */
    public static function fromError(Throwable $e): self
    {
        return new static($e);
    }
    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['errorCode' => $this->statusCode, 'errorMessage' => $this->error->getMessage()];
    }
}
