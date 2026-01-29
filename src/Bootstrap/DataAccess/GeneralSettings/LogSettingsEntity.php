<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\LogRecordsLifetime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\LogSettings;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class LogSettingsEntity
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class LogSettingsEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected string $mode;
    protected LogSettings $logSettings;
    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('mode');
        return new EntityConfiguration($indexMap, 'LogSettings');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->mode = $data['mode'];
        $logSettings = $data['logSettings'];
        $this->logSettings = new LogSettings($logSettings['debugMode'], LogRecordsLifetime::create($logSettings['logRecordsLifetime']));
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['mode'] = $this->mode;
        $data['logSettings'] = ['debugMode' => $this->logSettings->isDebugMode(), 'logRecordsLifetime' => $this->logSettings->getLogRecordsLifetime()->getDays()];
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
    public function getLogSettings(): LogSettings
    {
        return $this->logSettings;
    }
    public function setLogSettings(LogSettings $logSettings): void
    {
        $this->logSettings = $logSettings;
    }
}
