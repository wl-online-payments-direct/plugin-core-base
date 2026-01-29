<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Exceptions\BaseTranslatableException;
/**
 * Class TranslatableErrorResponse
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Response
 */
class TranslatableErrorResponse extends ErrorResponse
{
    /**
     * @var BaseTranslatableException
     */
    protected \Throwable $error;
    /**
     * @param BaseTranslatableException $error
     */
    public function __construct(BaseTranslatableException $error)
    {
        parent::__construct($error);
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['statusCode' => $this->error->getCode(), 'errorCode' => $this->error->getTranslatableLabel()->getCode(), 'errorMessage' => $this->error->getTranslatableLabel()->getMessage(), 'errorParameters' => $this->error->getTranslatableLabel()->getParams()];
    }
}
