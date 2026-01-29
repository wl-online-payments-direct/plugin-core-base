<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInput;
use OnlinePayments\Sdk\Domain\GPayThreeDSecure;
use OnlinePayments\Sdk\Domain\MobilePaymentProduct320SpecificInput;
/**
 * Class GooglePaySpecificRequestTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class GooglePaySpecificRequestTransformer
{
    public static function transform(CardPaymentMethodSpecificInput $cardPaymentMethodSpecificInput): MobilePaymentProduct320SpecificInput
    {
        $mobilePaymentProduct320SpecificInput = new MobilePaymentProduct320SpecificInput();
        $gPayThreeDSecure = new GPayThreeDSecure();
        $threeDSecure = $cardPaymentMethodSpecificInput->getThreeDSecure();
        $gPayThreeDSecure->setSkipAuthentication($threeDSecure->getSkipAuthentication());
        $gPayThreeDSecure->setChallengeIndicator($threeDSecure->getchallengeIndicator());
        $gPayThreeDSecure->setRedirectionData($threeDSecure->getRedirectionData());
        $gPayThreeDSecure->setExemptionRequest($threeDSecure->getexemptionRequest());
        $mobilePaymentProduct320SpecificInput->setThreeDSecure($gPayThreeDSecure);
        return $mobilePaymentProduct320SpecificInput;
    }
}
