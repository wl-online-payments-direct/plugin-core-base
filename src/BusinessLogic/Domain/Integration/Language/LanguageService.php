<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Language;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Language\Language;
/**
 * Interface LanguageService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Language
 */
interface LanguageService
{
    /**
     * @return Language[]
     */
    public function getEnabledLanguages(): array;
}
