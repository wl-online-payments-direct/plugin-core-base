<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Exceptions\InvalidTranslatableArrayException;
/**
 * Class TranslationCollection
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Translations\Model
 */
class TranslationCollection
{
    /**
     * @var Translation[]
     */
    private array $translations = [];
    /**
     * @var Translation
     */
    private Translation $defaultTranslation;
    /**
     * TranslationCollection constructor.
     *
     * @param Translation $defaultTranslation
     */
    public function __construct(Translation $defaultTranslation)
    {
        $this->defaultTranslation = $defaultTranslation;
        $this->addTranslation($defaultTranslation);
    }
    public function getTranslations(): array
    {
        return $this->translations;
    }
    /**
     * @return Translation
     */
    public function getDefaultTranslation(): Translation
    {
        return $this->defaultTranslation;
    }
    /**
     * Adds a translation to the collection.
     *
     * @param Translation $translation
     *
     * @return void
     */
    public function addTranslation(Translation $translation): void
    {
        $this->translations[$translation->getLocaleCode()] = $translation;
    }
    /**
     * Retrieves a translation by locale code or returns the default if not found.
     *
     * @param string $localeCode
     *
     * @return Translation
     */
    public function getTranslation(string $localeCode): Translation
    {
        return $this->translations[$localeCode] ?? $this->defaultTranslation;
    }
    /**
     * Retrieves a translation message by locale code or returns the default if not found.
     *
     * @param string|null $localeCode
     *
     * @return string
     */
    public function getTranslationMessage(?string $localeCode): string
    {
        if (!$localeCode) {
            return $this->defaultTranslation->getMessage();
        }
        return $this->getTranslation($localeCode)->getMessage();
    }
    /**
     * Retrieves all translations as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->translations as $translation) {
            $result[] = $translation->toArray();
        }
        return $result;
    }
    /**
     * Creates a TranslationCollection from an array of translation data.
     * If the array is empty, creates a default translation with an empty string.
     * If no default is specified, uses the first translation as default.
     *
     * @param array $inputArray
     *
     * @return TranslationCollection
     *
     * @throws InvalidTranslatableArrayException
     */
    public static function fromArray(array $inputArray): TranslationCollection
    {
        if (empty($inputArray)) {
            $defaultTranslation = new Translation('default', '');
            return new self($defaultTranslation);
        }
        $defaultTranslation = null;
        foreach ($inputArray as $data) {
            if (isset($data['locale']) && $data['locale'] === 'default') {
                $defaultTranslation = Translation::fromArray($data);
                break;
            }
        }
        if (!isset($inputArray[0]) || !isset($inputArray[0]['locale']) || !isset($inputArray[0]['value'])) {
            throw new InvalidTranslatableArrayException(new TranslatableLabel('Input array cannot be empty', 'translatableLabel.emptyArray'));
        }
        if ($defaultTranslation === null) {
            $defaultTranslation = Translation::fromArray($inputArray[0]);
        }
        $collection = new self($defaultTranslation);
        foreach ($inputArray as $data) {
            $collection->addTranslation(Translation::fromArray($data));
        }
        return $collection;
    }
}
