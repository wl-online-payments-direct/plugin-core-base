<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Address;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer\Customer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\LineItem;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\LineItemCollection;
use OnlinePayments\Sdk\Domain\AddressPersonal;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\BrowserData;
use OnlinePayments\Sdk\Domain\ContactDetails;
use OnlinePayments\Sdk\Domain\Customer as SdkCustomer;
use OnlinePayments\Sdk\Domain\CustomerDevice;
use OnlinePayments\Sdk\Domain\Discount;
use OnlinePayments\Sdk\Domain\LineItem as SdkLineItem;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\OrderLineDetails;
use OnlinePayments\Sdk\Domain\OrderReferences;
use OnlinePayments\Sdk\Domain\PersonalInformation;
use OnlinePayments\Sdk\Domain\PersonalName;
use OnlinePayments\Sdk\Domain\Shipping;
use OnlinePayments\Sdk\Domain\ShoppingCart;
/**
 * Class OrderTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class OrderTransformer
{
    public static function transform(Cart $cart): Order
    {
        $order = new Order();
        $amountOfMoney = new AmountOfMoney();
        $amountOfMoney->setCurrencyCode((string) $cart->getTotal()->getCurrency());
        $amountOfMoney->setAmount($cart->getTotal()->getValue());
        $order->setAmountOfMoney($amountOfMoney);
        $reference = new OrderReferences();
        $reference->setMerchantReference($cart->getMerchantReference());
        $order->setReferences($reference);
        if ($cart->getDiscount()) {
            $discount = new Discount();
            $discount->setAmount($cart->getDiscount()->getValue());
            $order->setDiscount($discount);
        }
        if ($cart->getShipping()) {
            $shipping = new Shipping();
            $shipping->setAddress(self::transformAddress($cart->getShipping()->getAddress()));
            $shipping->setEmailAddress($cart->getShipping()->getContactDetails()->getEmail());
            $shipping->setShippingCost($cart->getShipping()->getCost()->getAmountExclTax()->getValue());
            $shipping->setShippingCostTax($cart->getShipping()->getCost()->getTaxAmount()->getValue());
            $order->setShipping($shipping);
        }
        $order->setCustomer(self::transformCustomer($cart->getCustomer()));
        if (!$cart->getLineItems()->isEmpty()) {
            $order->setShoppingCart(self::transformLineItems($cart->getLineItems()));
        }
        return $order;
    }
    private static function transformAddress(Address $cartAddress): AddressPersonal
    {
        $address = new AddressPersonal();
        $address->setCountryCode((string) $cartAddress->getCountry());
        $address->setCity($cartAddress->getCity());
        $address->setZip($cartAddress->getZip());
        $address->setStreet($cartAddress->getStreet());
        $address->setHouseNumber($cartAddress->getHouseNumber());
        $address->setAdditionalInfo($cartAddress->getAdditionalInfo());
        $address->setState($cartAddress->getState());
        $personalInfo = $cartAddress->getPersonalInformation();
        if ($personalInfo) {
            $personalName = new PersonalName();
            $personalName->setFirstName($cartAddress->getPersonalInformation()->getFirstName());
            $personalName->setSurname($cartAddress->getPersonalInformation()->getLastName());
            $personalName->setTitle($cartAddress->getPersonalInformation()->getTitle());
            $address->setName($personalName);
        }
        return $address;
    }
    private static function transformCustomer(Customer $cartCustomer): SdkCustomer
    {
        $customer = new SdkCustomer();
        $contactDetails = new ContactDetails();
        $contactDetails->setEmailAddress($cartCustomer->getContactDetails()->getEmail());
        $customer->setContactDetails($contactDetails);
        $customer->setLocale($cartCustomer->getLocale());
        $customer->setMerchantCustomerId($cartCustomer->getMerchantCustomerId());
        $personalInfo = $cartCustomer->getBillingAddress()->getPersonalInformation();
        if ($personalInfo) {
            $personalInformation = new PersonalInformation();
            $personalName = new PersonalName();
            $personalName->setFirstName($cartCustomer->getBillingAddress()->getPersonalInformation()->getFirstName());
            $personalName->setSurname($cartCustomer->getBillingAddress()->getPersonalInformation()->getLastName());
            $personalName->setTitle($cartCustomer->getBillingAddress()->getPersonalInformation()->getTitle());
            $personalInformation->setName($personalName);
            $personalInformation->setGender($cartCustomer->getBillingAddress()->getPersonalInformation()->getGender());
            $customer->setPersonalInformation($personalInformation);
        }
        if ($cartCustomer->getDevice()) {
            $device = new CustomerDevice();
            $device->setAcceptHeader($cartCustomer->getDevice()->getAcceptHeader());
            $device->setUserAgent($cartCustomer->getDevice()->getUserAgent());
            $device->setIpAddress($cartCustomer->getDevice()->getIpAddress());
            $device->setLocale($cartCustomer->getLocale());
            $device->setTimezoneOffsetUtcMinutes($cartCustomer->getDevice()->getTimezoneOffsetUtcMinutes());
            $browserData = new BrowserData();
            $browserData->setColorDepth($cartCustomer->getDevice()->getColorDepth());
            $browserData->setJavaEnabled($cartCustomer->getDevice()->isJavaEnabled());
            $browserData->setScreenHeight($cartCustomer->getDevice()->getScreenHeight());
            $browserData->setScreenWidth($cartCustomer->getDevice()->getScreenWidth());
            $device->setBrowserData($browserData);
            $customer->setDevice($device);
        }
        $customer->setBillingAddress(self::transformAddress($cartCustomer->getBillingAddress()));
        return $customer;
    }
    private static function transformLineItems(LineItemCollection $lineItems): ShoppingCart
    {
        $shoppingCart = new ShoppingCart();
        $shoppingCart->setItems(array_map(function (LineItem $lineItem) {
            $cartItem = new SdkLineItem();
            $amountOfMoney = new AmountOfMoney();
            $amountOfMoney->setCurrencyCode((string) $lineItem->getTotal()->getCurrency());
            $amountOfMoney->setAmount($lineItem->getTotal()->getValue());
            $orderLineDetails = new OrderLineDetails();
            $orderLineDetails->setProductPrice($lineItem->getUnitPrice()->getAmountExclTax()->getValue());
            $orderLineDetails->setTaxAmount($lineItem->getUnitPrice()->getTaxAmount()->getValue());
            $orderLineDetails->setQuantity($lineItem->getQuantity());
            $orderLineDetails->setDiscountAmount(null !== $lineItem->getDiscount() ? $lineItem->getDiscount()->getValue() : null);
            $orderLineDetails->setProductName($lineItem->getProduct()->getName());
            $orderLineDetails->setProductCode($lineItem->getProduct()->getUpcCode());
            $orderLineDetails->setProductType($lineItem->getProduct()->getType());
            $orderLineDetails->setUnit($lineItem->getProduct()->getUnit());
            $cartItem->setAmountOfMoney($amountOfMoney);
            $cartItem->setOrderLineDetails($orderLineDetails);
            return $cartItem;
        }, $lineItems->toArray()));
        return $shoppingCart;
    }
}
