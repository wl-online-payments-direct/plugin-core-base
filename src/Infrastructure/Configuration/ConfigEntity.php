<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Configuration;

use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class ConfigEntity.
 *
 * @package OnlinePayments\Core\Infrastructure\Configuration
 */
class ConfigEntity extends Entity
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Configuration property name.
     *
     * @var ?string
     */
    protected ?string $name = null;
    /**
     * Configuration property value.
     *
     * @var mixed
     */
    protected $value;
    /**
     * Configuration context identifier.
     *
     * @var ?string
     */
    protected ?string $context = null;
    /**
     * Array of field names.
     *
     * @var array
     */
    protected array $fields = ['id', 'name', 'value', 'context'];
    /**
     * Returns entity configuration object.
     *
     * @return EntityConfiguration Configuration object.
     */
    public function getConfig(): EntityConfiguration
    {
        $map = new IndexMap();
        $map->addStringIndex('name')->addStringIndex('context');
        return new EntityConfiguration($map, 'Configuration');
    }
    /**
     * Gets configuration property name.
     *
     * @return string Configuration property name.
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Sets configuration property name.
     *
     * @param string $name Configuration property name.
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    /**
     * Gets Configuration property value.
     *
     * @return mixed Configuration property value.
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Sets Configuration property value.
     *
     * @param mixed $value Configuration property value.
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
    /**
     * Sets context on config entity.
     *
     * @param string $context
     */
    public function setContext(string $context)
    {
        $this->context = $context;
    }
    /**
     * Retrieves config value context.
     *
     * @return ?string Context value.
     */
    public function getContext(): ?string
    {
        return $this->context;
    }
}
