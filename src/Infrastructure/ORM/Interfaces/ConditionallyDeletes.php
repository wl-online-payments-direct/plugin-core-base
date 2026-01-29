<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces;

use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Interface ConditionallyDeletes.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\Interfaces
 */
interface ConditionallyDeletes extends RepositoryInterface
{
    /**
     * @param QueryFilter|null $queryFilter
     *
     * @return mixed
     */
    public function deleteWhere(QueryFilter $queryFilter = null);
}
