<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Language;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Language\Exception\InvalidIsoCodeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class LanguageCode
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Language
 */
class LanguageCode
{
    public const LIST_LANG_TO_COUNTRY = ['aa' => 'ET', 'ab' => 'GE', 'ae' => 'IR', 'af' => 'ZA', 'ak' => 'GH', 'am' => 'ET', 'an' => 'ES', 'ar' => 'SA', 'as' => 'IN', 'av' => 'RU', 'ay' => 'BO', 'az' => 'AZ', 'ba' => 'RU', 'be' => 'BY', 'bg' => 'BG', 'bi' => 'VU', 'bm' => 'ML', 'bn' => 'BD', 'bo' => 'CN', 'br' => 'FR', 'bs' => 'BA', 'ca' => 'ES', 'ce' => 'RU', 'ch' => 'GU', 'co' => 'FR', 'cr' => 'CA', 'cs' => 'CZ', 'cu' => 'RU', 'cv' => 'RU', 'cy' => 'GB', 'da' => 'DK', 'de' => 'DE', 'dv' => 'MV', 'dz' => 'BT', 'ee' => 'GH', 'el' => 'GR', 'en' => 'GB', 'eo' => 'EU', 'es' => 'ES', 'et' => 'EE', 'eu' => 'ES', 'fa' => 'IR', 'ff' => 'SN', 'fi' => 'FI', 'fj' => 'FJ', 'fo' => 'FO', 'fr' => 'FR', 'fy' => 'NL', 'ga' => 'IE', 'gd' => 'GB', 'gl' => 'ES', 'gn' => 'PY', 'gu' => 'IN', 'gv' => 'IM', 'ha' => 'NG', 'he' => 'IL', 'hi' => 'IN', 'ho' => 'PG', 'hr' => 'HR', 'ht' => 'HT', 'hu' => 'HU', 'hy' => 'AM', 'hz' => 'NA', 'ia' => 'EU', 'id' => 'ID', 'ie' => 'EU', 'ig' => 'NG', 'ii' => 'CN', 'ik' => 'US', 'io' => 'EU', 'is' => 'IS', 'it' => 'IT', 'iu' => 'CA', 'ja' => 'JP', 'jv' => 'ID', 'ka' => 'GE', 'kg' => 'CG', 'ki' => 'KE', 'kj' => 'NA', 'kk' => 'KZ', 'kl' => 'GL', 'km' => 'KH', 'kn' => 'IN', 'ko' => 'KR', 'kr' => 'NG', 'ks' => 'IN', 'ku' => 'IQ', 'kv' => 'RU', 'kw' => 'GB', 'ky' => 'KG', 'la' => 'VA', 'lb' => 'LU', 'lg' => 'UG', 'li' => 'NL', 'ln' => 'CD', 'lo' => 'LA', 'lt' => 'LT', 'lu' => 'CD', 'lv' => 'LV', 'mg' => 'MG', 'mh' => 'MH', 'mi' => 'NZ', 'mk' => 'MK', 'ml' => 'IN', 'mn' => 'MN', 'mr' => 'IN', 'ms' => 'MY', 'mt' => 'MT', 'my' => 'MM', 'na' => 'NR', 'nb' => 'NO', 'nd' => 'ZW', 'ne' => 'NP', 'ng' => 'NA', 'nl' => 'NL', 'nn' => 'NO', 'no' => 'NO', 'nr' => 'ZA', 'nv' => 'US', 'ny' => 'MW', 'oc' => 'FR', 'oj' => 'CA', 'om' => 'ET', 'or' => 'IN', 'os' => 'RU', 'pa' => 'IN', 'pi' => 'IN', 'pl' => 'PL', 'ps' => 'AF', 'pt' => 'PT', 'qu' => 'PE', 'rm' => 'CH', 'rn' => 'BI', 'ro' => 'RO', 'ru' => 'RU', 'rw' => 'RW', 'sa' => 'IN', 'sc' => 'IT', 'sd' => 'PK', 'se' => 'NO', 'sg' => 'CF', 'si' => 'LK', 'sk' => 'SK', 'sl' => 'SI', 'sm' => 'WS', 'sn' => 'ZW', 'so' => 'SO', 'sq' => 'AL', 'sr' => 'RS', 'ss' => 'SZ', 'st' => 'LS', 'su' => 'ID', 'sv' => 'SE', 'sw' => 'TZ', 'ta' => 'IN', 'te' => 'IN', 'tg' => 'TJ', 'th' => 'TH', 'ti' => 'ER', 'tk' => 'TM', 'tl' => 'PH', 'tn' => 'BW', 'to' => 'TO', 'tr' => 'TR', 'ts' => 'ZA', 'tt' => 'RU', 'tw' => 'GH', 'ty' => 'PF', 'ug' => 'CN', 'uk' => 'UA', 'ur' => 'PK', 'uz' => 'UZ', 've' => 'ZA', 'vi' => 'VN', 'vo' => 'EU', 'wa' => 'BE', 'wo' => 'SN', 'xh' => 'ZA', 'yi' => 'IL', 'yo' => 'NG', 'za' => 'CN', 'zh' => 'CN', 'zu' => 'ZA'];
    private string $language;
    private string $country;
    /**
     * @param string $language
     * @param string $country
     */
    private function __construct(string $language, string $country)
    {
        $this->language = $language;
        $this->country = $country;
    }
    /**
     * @param string $isoCode
     *
     * @return LanguageCode
     *
     * @throws InvalidIsoCodeException
     */
    public static function fromIso(string $isoCode): LanguageCode
    {
        $parts = explode('_', $isoCode);
        if (isset($parts[1])) {
            return new LanguageCode($parts[0], $parts[1]);
        }
        $parts = explode('-', $isoCode);
        if (isset($parts[1])) {
            return new LanguageCode($parts[0], $parts[1]);
        }
        if (isset(self::LIST_LANG_TO_COUNTRY[$isoCode])) {
            return new LanguageCode($isoCode, self::LIST_LANG_TO_COUNTRY[$isoCode]);
        }
        throw new InvalidIsoCodeException(new TranslatableLabel('Invalid ISO code', 'general.error.invalidIsoCode'));
    }
    /**
     * @return string
     */
    public function getFormattedWithDash(): string
    {
        return strtolower($this->language) . '-' . strtoupper($this->country);
    }
    /**
     * @return string
     */
    public function getFormattedWithUnderscore(): string
    {
        return strtolower($this->language) . '_' . strtoupper($this->country);
    }
    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }
    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
}
