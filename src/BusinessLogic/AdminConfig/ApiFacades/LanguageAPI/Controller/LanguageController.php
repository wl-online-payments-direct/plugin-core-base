<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\LanguageAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\LanguageAPI\Response\LanguageResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Language\LanguageService;
/**
 * Class LanguageController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\LanguageAPI\Controller
 */
class LanguageController
{
    protected LanguageService $languageService;
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }
    public function getLanguages(): LanguageResponse
    {
        return new LanguageResponse($this->languageService->getEnabledLanguages());
    }
}
