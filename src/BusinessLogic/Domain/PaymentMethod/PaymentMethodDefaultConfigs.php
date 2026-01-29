<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

/**
 * Class PaymentMethodDefaultConfigs
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class PaymentMethodDefaultConfigs
{
    public const PAYMENT_METHOD_CONFIGS = [PaymentProductId::CARDS => ['name' => ['language' => 'EN', 'translation' => self::CREDIT_CARD], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT, self::TOKENIZATION]], PaymentProductId::AMERICAN_EXPRESS => ['name' => ['language' => 'EN', 'translation' => self::AMERICAN_EXPRESS], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::BANCONTACT => ['name' => ['language' => 'EN', 'translation' => self::BANCONTACT], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::CARTE_BANCAIRE => ['name' => ['language' => 'EN', 'translation' => self::CARTE_BANCAIRE], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::DINERS_CLUB => ['name' => ['language' => 'EN', 'translation' => self::DINERS_CLUB], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::DISCOVER => ['name' => ['language' => 'EN', 'translation' => self::DISCOVER], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::JCB => ['name' => ['language' => 'EN', 'translation' => self::JCB], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::MASTERCARD => ['name' => ['language' => 'EN', 'translation' => self::MASTERCARD], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::MAESTRO => ['name' => ['language' => 'EN', 'translation' => self::MAESTRO], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::UPI => ['name' => ['language' => 'EN', 'translation' => self::UPI], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::VISA => ['name' => ['language' => 'EN', 'translation' => self::VISA], 'paymentGroup' => self::CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::HOSTED_CHECKOUT => ['name' => ['language' => 'EN', 'translation' => self::HOSTED_CHECKOUT], 'paymentGroup' => self::HOSTED, 'integrationTypes' => [self::HOSTED_PAGE]], PaymentProductId::ALIPAY => ['name' => ['language' => 'EN', 'translation' => self::ALIPAY], 'paymentGroup' => self::MOBILE, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::APPLE_PAY => ['name' => ['language' => 'EN', 'translation' => self::APPLE_PAY], 'paymentGroup' => self::MOBILE, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::BANK_TRANSFER => ['name' => ['language' => 'EN', 'translation' => self::BANK_TRANSFER], 'paymentGroup' => self::REAL_TIME_BANKING, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::CADO => ['name' => ['language' => 'EN', 'translation' => self::BIMPLI_CADO], 'paymentGroup' => self::GIFT_CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::BIZUM => ['name' => ['language' => 'EN', 'translation' => self::BIZUM], 'paymentGroup' => self::REAL_TIME_BANKING, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::CADHOC => ['name' => ['language' => 'EN', 'translation' => self::CADHOC], 'paymentGroup' => self::GIFT_CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::CHEQUE_VACANCES_CONNECT => ['name' => ['language' => 'EN', 'translation' => self::CVCO], 'paymentGroup' => self::PREPAID, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::CETELEM => ['name' => ['language' => 'EN', 'translation' => self::CETELEM], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::COFIDIS => ['name' => ['language' => 'EN', 'translation' => self::COFIDIS], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::CPAY => ['name' => ['language' => 'EN', 'translation' => self::CPAY], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::EPS => ['name' => ['language' => 'EN', 'translation' => self::EPS], 'paymentGroup' => self::REAL_TIME_BANKING, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::GOOGLE_PAY => ['name' => ['language' => 'EN', 'translation' => self::GOOGLE_PAY], 'paymentGroup' => self::MOBILE, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::IDEAL => ['name' => ['language' => 'EN', 'translation' => self::IDEAL], 'paymentGroup' => self::REAL_TIME_BANKING, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::ILLICADO => ['name' => ['language' => 'EN', 'translation' => self::ILLICADO], 'paymentGroup' => self::GIFT_CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::INTERSOLVE => ['name' => ['language' => 'EN', 'translation' => self::INTERSOLVE], 'paymentGroup' => self::GIFT_CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::KLARNA => ['name' => ['language' => 'EN', 'translation' => self::KLARNA], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::MB_WAY => ['name' => ['language' => 'EN', 'translation' => self::MBWAY], 'paymentGroup' => self::E_WALLET, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::MEALVOUCHERS => ['name' => ['language' => 'EN', 'translation' => self::MEALVOUCHERS], 'paymentGroup' => self::PREPAID, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::MULTIBANCO => ['name' => ['language' => 'EN', 'translation' => self::MULTIBANCO], 'paymentGroup' => self::POSTPAID, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::ONEY_3X => ['name' => ['language' => 'EN', 'translation' => self::ONEY_3X], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::ONEY_4X => ['name' => ['language' => 'EN', 'translation' => self::ONEY_4X], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::ONEY_BANK_CARD => ['name' => ['language' => 'EN', 'translation' => self::ONEY_BANK_CARD], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::ONEY_FINANCEMENT_LONG => ['name' => ['language' => 'EN', 'translation' => self::ONEY_FINANCEMENT_LONG], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::ONEY_BRANDED_GIFT_CARD => ['name' => ['language' => 'EN', 'translation' => self::ONEY_BRANDED_GIFT_CARD], 'paymentGroup' => self::GIFT_CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::PRZELEWY24 => ['name' => ['language' => 'EN', 'translation' => self::PRZELEWY24], 'paymentGroup' => self::REAL_TIME_BANKING, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::PAYPAL => ['name' => ['language' => 'EN', 'translation' => self::PAYPAL], 'paymentGroup' => self::E_WALLET, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::POSTFINANCE_PAY => ['name' => ['language' => 'EN', 'translation' => self::POSTFINANCE_PAY], 'paymentGroup' => self::REAL_TIME_BANKING, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::SEPA_DIRECT_DEBIT => ['name' => ['language' => 'EN', 'translation' => self::SEPA_DIRECT_DEBIT], 'paymentGroup' => self::DIRECT_DEBIT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::SOFINCO => ['name' => ['language' => 'EN', 'translation' => self::SOFINCO], 'paymentGroup' => self::INSTALMENT, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::SPIRIT_OF_CADEAU => ['name' => ['language' => 'EN', 'translation' => self::SPIRIT_OF_CADEAU], 'paymentGroup' => self::GIFT_CARDS, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::TWINT => ['name' => ['language' => 'EN', 'translation' => self::TWINT], 'paymentGroup' => self::REAL_TIME_BANKING, 'integrationTypes' => [self::REDIRECT]], PaymentProductId::WECHAT_PAY => ['name' => ['language' => 'EN', 'translation' => self::WECHAT_PAY], 'paymentGroup' => self::MOBILE, 'integrationTypes' => [self::REDIRECT]]];
    public const ALIPAY = 'Alipay+';
    public const APPLE_PAY = 'Apple Pay';
    public const BANK_TRANSFER = 'Bank Transfer by %s';
    public const BIMPLI_CADO = 'Bilmpli CADO';
    public const BIZUM = 'Bizum';
    public const CADHOC = 'Cadhoc';
    public const CVCO = 'Chèque-Vacances Connect';
    public const CETELEM = 'Cetelem 3x4x';
    public const COFIDIS = 'Cofidis 3x4x';
    public const CPAY = 'Cpay';
    public const CREDIT_CARD = 'Credit cards';
    public const AMERICAN_EXPRESS = 'American Express';
    public const BANCONTACT = 'Bancontact';
    public const CARTE_BANCAIRE = 'Carte Bancaire';
    public const DINERS_CLUB = 'Diners Club';
    public const DISCOVER = 'Discover';
    public const JCB = 'JCB';
    public const MASTERCARD = 'Mastercard';
    public const MAESTRO = 'Maestra';
    public const UPI = 'UPI - UnionPay International';
    public const VISA = 'Visa';
    public const EPS = 'EPS';
    public const GOOGLE_PAY = 'Google Pay';
    public const HOSTED_CHECKOUT = 'Hosted Checkout (Redirect To %s)';
    public const IDEAL = 'Ideal';
    public const ILLICADO = 'Illicado';
    public const INTERSOLVE = 'Intersolve';
    public const KLARNA = 'Klarna';
    public const MBWAY = 'MB Way';
    public const MEALVOUCHERS = 'Mealvouchers';
    public const MULTIBANCO = 'Multibanco';
    public const ONEY_3X = 'Oney 3x';
    public const ONEY_4X = 'Oney 4x';
    public const ONEY_BANK_CARD = 'Oney Bank Card';
    public const ONEY_FINANCEMENT_LONG = 'Oney Financement Long';
    public const ONEY_BRANDED_GIFT_CARD = 'Oney Branded Gift Card';
    public const PRZELEWY24 = 'Przelewy24';
    public const PAYPAL = 'Paypal';
    public const POSTFINANCE_PAY = 'Postfinance Pay';
    public const SEPA_DIRECT_DEBIT = 'SEPA Direct Debit';
    public const SOFINCO = 'Sofinco 3x/4x';
    public const SPIRIT_OF_CADEAU = 'Spirit of Cadeau';
    public const TWINT = 'Twint';
    public const WECHAT_PAY = 'WeChat Pay';
    // integration types
    public const HOSTED_PAGE = 'hosted';
    public const REDIRECT = 'redirect';
    public const TOKENIZATION = 'tokenization';
    // payment groups
    public const MOBILE = 'mobile';
    public const REAL_TIME_BANKING = 'realTimeBanking';
    public const GIFT_CARDS = 'giftCards';
    public const PREPAID = 'prepaid';
    public const INSTALMENT = 'instalment';
    public const CARDS = 'cards';
    public const E_WALLET = 'eWallet';
    public const POSTPAID = 'postpaid';
    public const DIRECT_DEBIT = 'directDebit';
    public const HOSTED = 'hosted';
    /**
     * @param string $productId
     * @param string $brand
     * @return array
     */
    public static function getName(string $productId, string $brand): array
    {
        $result = [];
        if (isset(self::PAYMENT_METHOD_CONFIGS[$productId]['name'])) {
            $result = self::PAYMENT_METHOD_CONFIGS[$productId]['name'];
            $result['translation'] = sprintf($result['translation'], $brand);
        }
        return $result;
    }
    public static function getPaymentGroup(string $productId): string
    {
        return self::PAYMENT_METHOD_CONFIGS[$productId]['paymentGroup'] ?? '';
    }
    public static function getIntegrationTypes(string $productId): array
    {
        return self::PAYMENT_METHOD_CONFIGS[$productId]['integrationTypes'] ?? [];
    }
}
