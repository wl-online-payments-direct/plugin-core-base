<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\LanguageAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Language\Language;
/**
 * Class LanguageResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\LanguageAPI\Response
 */
class LanguageResponse extends Response
{
    /**
     * @var Language[]
     */
    private array $languages;
    /**
     * @param Language[] $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->languages as $language) {
            $result[strtoupper($language->getCode())] = ['code' => $language->getCode(), 'logo' => $language->getLogo()];
        }
        return $result;
    }
}
