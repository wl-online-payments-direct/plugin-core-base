<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Configuration;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
use WOP\OnlinePayments\Core\Infrastructure\Configuration\ConfigurationManager as InfrastructureConfigurationManager;
/**
 * Class ConfigurationManager.
 *
 * @package OnlinePayments\Core\Bootstrap\Configuration
 */
class ConfigurationManager extends InfrastructureConfigurationManager
{
    /**
     * Infrastructure context must always  match tenantId
     *
     * @return string
     */
    public function getContext(): string
    {
        return TenantContext::getInstance()->getTenantId();
    }
}
