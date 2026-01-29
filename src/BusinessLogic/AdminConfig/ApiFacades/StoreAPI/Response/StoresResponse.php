<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models\Store;
/**
 * Class StoresResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Response
 */
class StoresResponse extends Response
{
    /**
     * @var Store[]
     */
    private array $stores;
    /**
     * @param Store[] $stores
     */
    public function __construct(array $stores)
    {
        $this->stores = $stores;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_map(static function (Store $store): array {
            return (new StoreResponse($store))->toArray();
        }, $this->stores);
    }
}
