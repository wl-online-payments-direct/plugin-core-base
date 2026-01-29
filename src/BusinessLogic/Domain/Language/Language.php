<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Language;

/**
 * Class Language
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Language
 */
class Language
{
    protected string $code;
    protected string $logo;
    /**
     * @param string $code
     * @param string $logo
     */
    public function __construct(string $code, string $logo)
    {
        $this->code = $code;
        $this->logo = $logo;
    }
    public function getCode(): string
    {
        return $this->code;
    }
    public function getLogo(): string
    {
        return $this->logo;
    }
}
