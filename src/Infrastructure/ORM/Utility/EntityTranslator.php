<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\Utility;

use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\EntityClassException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\IntermediateObject;
/**
 * Class EntityTranslator.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\Utility
 */
class EntityTranslator
{
    /**
     * @var ?string
     */
    private ?string $entityClass = null;
    /**
     * @param string $entityClass
     *
     * @throws EntityClassException
     *
     * @return void
     */
    public function init(string $entityClass): void
    {
        if (!is_subclass_of($entityClass, Entity::getClassName())) {
            throw new EntityClassException("Class {$entityClass} is not implementation of Entity");
        }
        $this->entityClass = $entityClass;
    }
    /**
     * Translate intermediate objects to concrete entities
     *
     * @param IntermediateObject[] $intermediateObjects
     *
     * @return Entity[]
     *
     * @throws EntityClassException
     */
    public function translate(array $intermediateObjects): array
    {
        if ($this->entityClass === null) {
            throw new EntityClassException('Entity translator must be initialized with entity class.');
        }
        $result = [];
        foreach ($intermediateObjects as $intermediateObject) {
            $result[] = $this->translateOne($intermediateObject);
        }
        return $result;
    }
    /**
     * Translates one intermediate object to concrete object
     *
     * @param IntermediateObject $intermediateObject
     *
     * @return Entity
     *
     * @throws EntityClassException
     */
    private function translateOne(IntermediateObject $intermediateObject): Entity
    {
        $data = json_decode($intermediateObject->getData(), \true);
        if (empty($data['class_name'])) {
            throw new EntityClassException('Entity has not provided class name.');
        }
        /** @var Entity $entity */
        $entity = new $data['class_name']();
        $entity->inflate($data);
        if (!$entity instanceof $this->entityClass) {
            throw new EntityClassException("Unserialized entity is not of class {$this->entityClass}");
        }
        return $entity;
    }
}
