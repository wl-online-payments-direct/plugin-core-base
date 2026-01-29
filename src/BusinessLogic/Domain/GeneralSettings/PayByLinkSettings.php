<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPayByLinkExpirationTimeException;
/**
 * Class PayByLinkSettings
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class PayByLinkSettings
{
    protected bool $enable;
    protected string $title;
    protected PayByLinkExpirationTime $expirationTime;
    /**
     * @param bool $enable
     * @param string $title
     * @param PayByLinkExpirationTime|null $expirationTime
     *
     * @throws InvalidPayByLinkExpirationTimeException
     */
    public function __construct(bool $enable = \false, string $title = '', ?PayByLinkExpirationTime $expirationTime = null)
    {
        $this->enable = $enable;
        $this->title = $title;
        $this->expirationTime = $expirationTime ?: PayByLinkExpirationTime::create(7);
    }
    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
    /**
     * @return PayByLinkExpirationTime
     */
    public function getExpirationTime(): PayByLinkExpirationTime
    {
        return $this->expirationTime;
    }
}
