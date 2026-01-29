<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Repositories;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
/**
 * Interface TokensRepositoryInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories
 */
interface TokensRepositoryInterface
{
    public function get(string $customerId, string $tokenId): ?Token;
    public function save(string $customerId, Token $token): void;
    /**
     * @param string $customerId
     * @return Token[]
     */
    public function getForCustomer(string $customerId): array;
    /**
     * @param Token[] $tokens
     * @return void
     */
    public function delete(array $tokens): void;
}
