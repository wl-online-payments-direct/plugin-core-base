<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer;

/**
 * Class Device.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer
 */
class Device
{
    private string $acceptHeader;
    private string $userAgent;
    private string $ipAddress;
    private int $colorDepth;
    private string $screenHeight;
    private string $screenWidth;
    private string $timezoneOffsetUtcMinutes;
    private bool $javaEnabled;
    public function __construct(string $acceptHeader, string $userAgent, string $ipAddress, int $colorDepth = 24, string $screenHeight = '1080', string $screenWidth = '1920', string $timezoneOffsetUtcMinutes = '', bool $javaEnabled = \false)
    {
        $this->acceptHeader = $acceptHeader;
        $this->userAgent = $userAgent;
        $this->ipAddress = $ipAddress;
        $this->colorDepth = $colorDepth;
        $this->screenHeight = $screenHeight;
        $this->screenWidth = $screenWidth;
        $this->timezoneOffsetUtcMinutes = $timezoneOffsetUtcMinutes;
        $this->javaEnabled = $javaEnabled;
    }
    public function getAcceptHeader(): string
    {
        return $this->acceptHeader;
    }
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }
    public function getColorDepth(): int
    {
        return $this->colorDepth;
    }
    public function getScreenHeight(): string
    {
        return $this->screenHeight;
    }
    public function getScreenWidth(): string
    {
        return $this->screenWidth;
    }
    public function getTimezoneOffsetUtcMinutes(): string
    {
        return $this->timezoneOffsetUtcMinutes;
    }
    public function isJavaEnabled(): bool
    {
        return $this->javaEnabled;
    }
}
