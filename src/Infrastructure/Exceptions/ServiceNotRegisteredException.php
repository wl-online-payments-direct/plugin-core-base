<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Exceptions;

/**
 * Class ServiceNotRegisteredException.
 *
 * @package OnlinePayments\Core\Infrastructure\Exceptions
 */
class ServiceNotRegisteredException extends BaseException
{
    /**
     * ServiceNotRegisteredException constructor.
     *
     * @param string $type Type of service. Should be fully qualified class name.
     */
    public function __construct(string $type)
    {
        parent::__construct("Service of type \"{$type}\" is not registered.");
    }
}
