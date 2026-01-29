<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Logo;

/**
 * Interface LogoUrlService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Logo
 */
interface LogoUrlService
{
    /**
     * @return string
     */
    public function getHostedCheckoutLogoUrl(): string;
    /**
     * @param string $productId
     *
     * @return string
     */
    public function getLogoUrl(string $productId): string;
}
