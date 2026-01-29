<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Version;

/**
 * Class VersionInfo
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Version
 */
class VersionInfo
{
    private string $installed;
    private string $latest;
    /**
     * @param string $installed
     * @param string $latest
     */
    public function __construct(string $installed, string $latest = '')
    {
        $this->installed = $installed;
        $this->latest = $latest;
    }
    public function getInstalled(): string
    {
        return $this->installed;
    }
    public function getLatest(): string
    {
        return $this->latest;
    }
}
