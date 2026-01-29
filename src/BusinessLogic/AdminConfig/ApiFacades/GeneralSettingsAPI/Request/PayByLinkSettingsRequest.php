<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPayByLinkExpirationTimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkExpirationTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkSettings;
/**
 * Class PayByLinkSettingsRequest
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request
 */
class PayByLinkSettingsRequest extends Request
{
    protected bool $enabled;
    protected string $title;
    protected int $expirationTime;
    /**
     * @param bool $enabled
     * @param string $title
     * @param int $expirationTime
     */
    public function __construct(bool $enabled, string $title, int $expirationTime)
    {
        $this->enabled = $enabled;
        $this->title = $title;
        $this->expirationTime = $expirationTime;
    }
    /**
     * @inheritDoc
     *
     * @throws InvalidPayByLinkExpirationTimeException
     */
    public function transformToDomainModel(): object
    {
        return new PayByLinkSettings($this->enabled, $this->title, PayByLinkExpirationTime::create($this->expirationTime));
    }
}
