<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Disconnect;

use DateTime;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\Utility\TimeProvider;
/**
 * Class DisconnectTime
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Disconnect
 */
class DisconnectTime extends Entity
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    protected string $storeId = '';
    protected ?DateTime $date = null;
    /**
     * @var string[]
     */
    protected array $fields = ['id', 'storeId', 'date'];
    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        return new EntityConfiguration($indexMap, 'DisconnectTime');
    }
    /**
     * @return string
     */
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    /**
     * @param string $storeId
     */
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }
    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['date'] = $this->getDate()->getTimestamp();
        return $data;
    }
    public function inflate(array $data): void
    {
        /** @var TimeProvider $timeProvider */
        $timeProvider = ServiceRegister::getService(TimeProvider::CLASS_NAME);
        $this->id = $data['id'];
        $this->storeId = $data['storeId'];
        $this->date = $timeProvider->getDateTime($data['date']);
    }
}
