<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Metadata;

/**
 * Interface MetadataProviderInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Metadata
 */
interface MetadataProviderInterface
{
    public function getMetadata(): Metadata;
}
