<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Tokens;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Repositories\TokensRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\ConditionallyDeletes;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class TokensRepository.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Tokens
 */
class TokensRepository implements TokensRepositoryInterface
{
    private ConditionallyDeletes $repository;
    private StoreContext $storeContext;
    public function __construct(ConditionallyDeletes $repository, StoreContext $storeContext)
    {
        $this->repository = $repository;
        $this->storeContext = $storeContext;
    }
    public function get(string $customerId, string $tokenId): ?Token
    {
        $tokenEntity = $this->getTokensEntity($customerId, $tokenId);
        return null !== $tokenEntity ? $tokenEntity->getToken() : null;
    }
    public function save(string $getCustomerId, Token $token): void
    {
        $entity = $this->getTokensEntity($getCustomerId, $token->getTokenId());
        if (null !== $entity) {
            $entity->setToken($token);
            $this->repository->update($entity);
            return;
        }
        $entity = new TokenEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setToken($token);
        $this->repository->save($entity);
    }
    /**
     * @param string $customerId
     * @return Token[]
     */
    public function getForCustomer(string $customerId): array
    {
        return array_map(function (TokenEntity $entity) {
            return $entity->getToken();
        }, $this->getCustomerTokensEntities($customerId));
    }
    public function delete(array $tokens): void
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('tokenId', Operators::IN, array_map(function (Token $token) {
            return $token->getTokenId();
        }, $tokens));
        $this->repository->deleteWhere($queryFilter);
    }
    private function getTokensEntity(string $customerId, string $tokenId): ?TokenEntity
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('customerId', Operators::EQUALS, $customerId)->where('tokenId', Operators::EQUALS, $tokenId);
        /** @var ?TokenEntity $token */
        $token = $this->repository->selectOne($queryFilter);
        return $token;
    }
    /**
     * @param string $customerId
     * @return TokenEntity[]
     */
    private function getCustomerTokensEntities(string $customerId): array
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('customerId', Operators::EQUALS, $customerId);
        /** @var TokenEntity[] $tokens */
        $tokens = $this->repository->select($queryFilter);
        return $tokens;
    }
}
