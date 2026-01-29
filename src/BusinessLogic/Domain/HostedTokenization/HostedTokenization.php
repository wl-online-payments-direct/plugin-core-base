<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization;

/**
 * Class HostedTokenization.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization
 */
class HostedTokenization
{
    private string $url;
    /**
     * @var string[]
     */
    private array $invalidTokens;
    /**
     * @param string $url
     * @param string[] $invalidTokens
     */
    public function __construct(string $url, array $invalidTokens)
    {
        $this->url = $url;
        $this->invalidTokens = $invalidTokens;
    }
    public function getUrl(): string
    {
        return $this->url;
    }
    public function getInvalidTokens(): array
    {
        return $this->invalidTokens;
    }
}
