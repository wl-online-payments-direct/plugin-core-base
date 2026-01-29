<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Tokens;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class TokenEntity.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Tokens
 */
class TokenEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected Token $token;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('customerId');
        $indexMap->addStringIndex('tokenId');
        return new EntityConfiguration($indexMap, 'TokenEntity');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->token = new Token($data['token']['customerId'], $data['token']['tokenId'], $data['token']['productId'], $data['token']['cardNumber'], $data['token']['expiryDate']);
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['token'] = ['customerId' => $this->token->getCustomerId(), 'tokenId' => $this->token->getTokenId(), 'productId' => $this->token->getProductId(), 'cardNumber' => $this->token->getCardNumber(), 'expiryDate' => $this->token->getExpiryDate()];
        return $data;
    }
    public function getCustomerId(): string
    {
        return $this->token->getCustomerId();
    }
    public function getTokenId(): string
    {
        return $this->token->getTokenId();
    }
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    public function getToken(): Token
    {
        return $this->token;
    }
    public function setToken(Token $token): void
    {
        $this->token = $token;
    }
}
