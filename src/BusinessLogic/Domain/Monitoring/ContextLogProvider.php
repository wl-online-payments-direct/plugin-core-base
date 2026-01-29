<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring;

/**
 * Class ContextLogProvider
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Monitoring
 */
class ContextLogProvider
{
    private static ?ContextLogProvider $instance = null;
    private ?string $currentOrder = null;
    private ?string $paymentNumber = null;
    private function __construct()
    {
    }
    public static function getInstance(): ContextLogProvider
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getCurrentOrder(): ?string
    {
        return $this->currentOrder;
    }
    public function setCurrentOrder(?string $currentOrder): void
    {
        $this->currentOrder = $currentOrder;
    }
    public function getPaymentNumber(): ?string
    {
        return $this->paymentNumber;
    }
    public function setPaymentNumber(?string $paymentNumber): void
    {
        $this->paymentNumber = $paymentNumber;
    }
}
