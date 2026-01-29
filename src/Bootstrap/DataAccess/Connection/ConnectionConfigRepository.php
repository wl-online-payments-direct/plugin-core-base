<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Connection;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionMode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Credentials;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories\ConnectionConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Encryption\Encryptor;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class PaymentMethodConfigRepository.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentMethod
 */
class ConnectionConfigRepository implements ConnectionConfigRepositoryInterface
{
    private RepositoryInterface $repository;
    private StoreContext $storeContext;
    protected Encryptor $encryptor;
    public function __construct(RepositoryInterface $repository, StoreContext $storeContext, Encryptor $encryptor)
    {
        $this->repository = $repository;
        $this->storeContext = $storeContext;
        $this->encryptor = $encryptor;
    }
    /**
     * @param ConnectionDetails $connectionDetails
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     * @throws InvalidConnectionDetailsException
     */
    public function saveConnection(ConnectionDetails $connectionDetails): void
    {
        $encrypted = new ConnectionDetails($connectionDetails->getMode(), $connectionDetails->getLiveCredentials() ? new Credentials($connectionDetails->getLiveCredentials()->getPspId(), $this->encryptor->encrypt($connectionDetails->getLiveCredentials()->getApiKey()), $this->encryptor->encrypt($connectionDetails->getLiveCredentials()->getApiSecret()), $this->encryptor->encrypt($connectionDetails->getLiveCredentials()->getWebhookKey()), $this->encryptor->encrypt($connectionDetails->getLiveCredentials()->getWebhookSecret())) : null, $connectionDetails->getTestCredentials() ? new Credentials($connectionDetails->getTestCredentials()->getPspId(), $this->encryptor->encrypt($connectionDetails->getTestCredentials()->getApiKey()), $this->encryptor->encrypt($connectionDetails->getTestCredentials()->getApiSecret()), $this->encryptor->encrypt($connectionDetails->getTestCredentials()->getWebhookKey()), $this->encryptor->encrypt($connectionDetails->getTestCredentials()->getWebhookSecret())) : null);
        $existingConnection = $this->getConnectionEntity();
        if ($existingConnection) {
            $existingConnection->setConnectionDetails($encrypted);
            $this->repository->update($existingConnection);
            return;
        }
        $entity = new ConnectionConfigEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setConnectionDetails($encrypted);
        $this->repository->save($entity);
    }
    /**
     * @return ConnectionDetails|null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getConnection(): ?ConnectionDetails
    {
        $entity = $this->getConnectionEntity();
        return $entity ? $entity->getConnectionDetails() : null;
    }
    /**
     * @return ConnectionConfigEntity|null
     *
     * @throws QueryFilterInvalidParamException
     * @throws InvalidConnectionDetailsException
     */
    protected function getConnectionEntity(): ?ConnectionConfigEntity
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId());
        /** @var ConnectionConfigEntity|null $entity */
        $entity = $this->repository->selectOne($queryFilter);
        if ($entity) {
            $entity->setConnectionDetails(new ConnectionDetails($entity->getConnectionDetails()->getMode(), $entity->getConnectionDetails()->getLiveCredentials() ? new Credentials($entity->getConnectionDetails()->getLiveCredentials()->getPspId(), $this->encryptor->decrypt($entity->getConnectionDetails()->getLiveCredentials()->getApiKey()), $this->encryptor->decrypt($entity->getConnectionDetails()->getLiveCredentials()->getApiSecret()), $this->encryptor->decrypt($entity->getConnectionDetails()->getLiveCredentials()->getWebhookKey()), $this->encryptor->decrypt($entity->getConnectionDetails()->getLiveCredentials()->getWebhookSecret())) : null, $entity->getConnectionDetails()->getTestCredentials() ? new Credentials($entity->getConnectionDetails()->getTestCredentials()->getPspId(), $this->encryptor->decrypt($entity->getConnectionDetails()->getTestCredentials()->getApiKey()), $this->encryptor->decrypt($entity->getConnectionDetails()->getTestCredentials()->getApiSecret()), $this->encryptor->decrypt($entity->getConnectionDetails()->getTestCredentials()->getWebhookKey()), $this->encryptor->decrypt($entity->getConnectionDetails()->getTestCredentials()->getWebhookSecret())) : null));
        }
        return $entity;
    }
    /**
     * @return ConnectionDetails|null
     */
    public function getOldestConnection(): ?ConnectionDetails
    {
        /** @var ConnectionConfigEntity $item */
        $item = $this->repository->selectOne(new QueryFilter());
        return $item ? $item->getConnectionDetails() : null;
    }
    /**
     * @return string
     */
    public function getOldestConnectedStore(): string
    {
        /** @var ConnectionConfigEntity $item */
        $item = $this->repository->selectOne(new QueryFilter());
        return $item ? $item->getStoreId() : '';
    }
    /**
     * @return void
     *
     * @throws InvalidConnectionDetailsException
     * @throws QueryFilterInvalidParamException
     */
    public function disconnect(): void
    {
        $entity = $this->getConnectionEntity();
        if (!$entity) {
            return;
        }
        $connectionDetails = null;
        if ($entity->getConnectionDetails()->getMode()->equals(ConnectionMode::test()) && $entity->getConnectionDetails()->getLiveCredentials() !== null) {
            $connectionDetails = new ConnectionDetails(ConnectionMode::live(), new Credentials($entity->getConnectionDetails()->getLiveCredentials()->getPspId(), $this->encryptor->encrypt($entity->getConnectionDetails()->getLiveCredentials()->getApiKey()), $this->encryptor->encrypt($entity->getConnectionDetails()->getLiveCredentials()->getApiSecret()), $this->encryptor->encrypt($entity->getConnectionDetails()->getLiveCredentials()->getWebhookKey()), $this->encryptor->encrypt($entity->getConnectionDetails()->getLiveCredentials()->getWebhookSecret())), null);
        }
        if ($entity->getConnectionDetails()->getMode()->equals(ConnectionMode::live()) && $entity->getConnectionDetails()->getTestCredentials() !== null) {
            $connectionDetails = new ConnectionDetails(ConnectionMode::test(), null, new Credentials($entity->getConnectionDetails()->getTestCredentials()->getPspId(), $this->encryptor->encrypt($entity->getConnectionDetails()->getTestCredentials()->getApiKey()), $this->encryptor->encrypt($entity->getConnectionDetails()->getTestCredentials()->getApiSecret()), $this->encryptor->encrypt($entity->getConnectionDetails()->getTestCredentials()->getWebhookKey()), $this->encryptor->encrypt($entity->getConnectionDetails()->getTestCredentials()->getWebhookSecret())));
        }
        if (!$connectionDetails) {
            $this->repository->delete($entity);
            return;
        }
        $entity->setConnectionDetails($connectionDetails);
        $this->repository->update($entity);
    }
}
