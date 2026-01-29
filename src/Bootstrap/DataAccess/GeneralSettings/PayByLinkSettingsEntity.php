<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkExpirationTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkSettings;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class PayByLinkSettingsEntity
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class PayByLinkSettingsEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected string $mode;
    protected PayByLinkSettings $payByLinkSettings;
    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('mode');
        return new EntityConfiguration($indexMap, 'PayByLinkSettings');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->mode = $data['mode'];
        $payByLinkSettings = $data['payByLinkSettings'];
        $this->payByLinkSettings = new PayByLinkSettings($payByLinkSettings['enabled'], $payByLinkSettings['title'], PayByLinkExpirationTime::create($payByLinkSettings['expirationTime']));
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['mode'] = $this->mode;
        $data['payByLinkSettings'] = ['enabled' => $this->payByLinkSettings->isEnable(), 'title' => $this->payByLinkSettings->getTitle(), 'expirationTime' => $this->payByLinkSettings->getExpirationTime()->getDays()];
        return $data;
    }
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    public function getMode(): string
    {
        return $this->mode;
    }
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }
    public function getPayByLinkSettings(): PayByLinkSettings
    {
        return $this->payByLinkSettings;
    }
    public function setPayByLinkSettings(PayByLinkSettings $payByLinkSettings): void
    {
        $this->payByLinkSettings = $payByLinkSettings;
    }
}
