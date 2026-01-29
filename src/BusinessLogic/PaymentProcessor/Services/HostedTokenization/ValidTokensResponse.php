<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedTokenization;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\HostedTokenization;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
/**
 * Class ValidTokensResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedTokenization
 */
class ValidTokensResponse
{
    private HostedTokenization $hostedTokenization;
    /**
     * @var Token[]
     */
    private array $tokens;
    /**
     * @param HostedTokenization $hostedTokenization
     * @param Token[] $tokens
     */
    public function __construct(HostedTokenization $hostedTokenization, array $tokens)
    {
        $this->hostedTokenization = $hostedTokenization;
        $this->tokens = $tokens;
    }
    public function getHostedTokenization(): HostedTokenization
    {
        return $this->hostedTokenization;
    }
    /**
     * @return Token[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
