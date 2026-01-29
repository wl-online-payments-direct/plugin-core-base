<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces;

use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Interface RepositoryInterface.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\Interfaces
 */
interface RepositoryInterface
{
    /**
     * Fully qualified name of this interface.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Returns full class name.
     *
     * @return string Full class name.
     */
    public static function getClassName(): string;
    /**
     * Sets repository entity.
     *
     * @param string $entityClass Repository entity class.
     */
    public function setEntityClass(string $entityClass);
    /**
     * Executes select query.
     *
     * @param QueryFilter|null $filter Filter for query.
     *
     * @return Entity[] A list of found entities ot empty array.
     */
    public function select(QueryFilter $filter = null): array;
    /**
     * Executes select query and returns first result.
     *
     * @param QueryFilter|null $filter Filter for query.
     *
     * @return Entity|null First found entity or NULL.
     */
    public function selectOne(QueryFilter $filter = null): ?Entity;
    /**
     * Executes insert query and returns ID of created entity. Entity will be updated with new ID.
     *
     * @param Entity $entity Entity to be saved.
     *
     * @return int Identifier of saved entity.
     */
    public function save(Entity $entity): int;
    /**
     * Executes update query and returns success flag.
     *
     * @param Entity $entity Entity to be updated.
     * @param QueryFilter|null $queryFilter
     *
     * @return bool TRUE if operation succeeded; otherwise, FALSE.
     */
    public function update(Entity $entity, QueryFilter $queryFilter = null): bool;
    /**
     * Executes delete query and returns success flag.
     *
     * @param Entity $entity Entity to be deleted.
     *
     * @return bool TRUE if operation succeeded; otherwise, FALSE.
     */
    public function delete(Entity $entity): bool;
    /**
     * Counts records that match filter criteria.
     *
     * @param QueryFilter|null $filter Filter for query.
     *
     * @return int Number of records that match filter criteria.
     */
    public function count(QueryFilter $filter = null): int;
}
