<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Exceptions\InvalidTranslatableArrayException;
class Translation
{
    /**
     * @var string
     */
    protected string $message;
    /**
     * @var string
     */
    protected string $localeCode;
    /**
     * @param string $message
     * @param string $localeCode
     */
    public function __construct(string $localeCode, string $message)
    {
        $this->message = $message;
        $this->localeCode = $localeCode;
    }
    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    /**
     * @return string
     */
    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }
    /**
     * @param array $input
     *
     * @return self
     *
     * @throws InvalidTranslatableArrayException
     */
    public static function fromArray(array $input): self
    {
        self::validateTranslatableArray($input);
        return new self($input['locale'], $input['value']);
    }
    /**
     * @return array
     */
    public function toArray(): array
    {
        return ['locale' => $this->getLocaleCode(), 'value' => $this->getMessage()];
    }
    /**
     * @param array $array
     *
     * @return void
     *
     * @throws InvalidTranslatableArrayException
     */
    private static function validateTranslatableArray(array $array): void
    {
        if (!isset($array['locale']) || !isset($array['value'])) {
            throw new InvalidTranslatableArrayException(new TranslatableLabel('Translatable array is invalid', 'translatableLabel.invalidArray'));
        }
    }
}
