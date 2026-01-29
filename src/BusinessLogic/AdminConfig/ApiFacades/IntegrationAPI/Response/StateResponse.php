<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\IntegrationAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
/**
 * Class StateResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\IntegrationAPI\Response
 */
class StateResponse extends Response
{
    /**
     * Connection string constant.
     */
    private const CONNECTION = 'connection';
    /**
     * Dashboard string constant.
     */
    private const PAYMENTS = 'payments';
    /**
     * String representation of state.
     *
     * @var string
     */
    private $state;
    /**
     * @param string $state
     */
    private function __construct(string $state)
    {
        $this->state = $state;
    }
    /**
     * Called when user is not logged in.
     *
     * @return StateResponse
     */
    public static function connection(): self
    {
        return new self(self::CONNECTION);
    }
    /**
     * Called when user is loggedIn.
     *
     * @return StateResponse
     */
    public static function payments(): self
    {
        return new self(self::PAYMENTS);
    }
    /**
     *  Transforms state to array.
     *
     * @return array Array representation of state.
     */
    public function toArray(): array
    {
        return ['state' => $this->state];
    }
}
