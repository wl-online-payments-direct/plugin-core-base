<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\VersionsAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Version\VersionInfo;
/**
 * Class VersionInfoResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\VersionsAPI\Response
 */
class VersionInfoResponse extends Response
{
    private VersionInfo $versionInfo;
    /**
     * @param VersionInfo $versionInfo
     */
    public function __construct(VersionInfo $versionInfo)
    {
        $this->versionInfo = $versionInfo;
    }
    public function toArray(): array
    {
        return ['installed' => $this->versionInfo->getInstalled(), 'latest' => $this->versionInfo->getLatest()];
    }
}
