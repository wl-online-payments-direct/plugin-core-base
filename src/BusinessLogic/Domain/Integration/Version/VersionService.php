<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Version;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Version\VersionInfo;
/**
 * Interface VersionService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Version
 */
interface VersionService
{
    /**
     * Retrieves plugin current and latest version.
     *
     * @return VersionInfo
     */
    public function getVersionInfo(): VersionInfo;
}
