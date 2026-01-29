<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentLink;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLink;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
/**
 * Class PaymentLinkEntity
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentLink
 */
class PaymentLinkEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected PaymentLink $paymentLink;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('paymentLinkId');
        $indexMap->addStringIndex('merchantReference');
        return new EntityConfiguration($indexMap, 'PaymentLinkEntity');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        /** @var TimeProviderInterface $timeProvider */
        $timeProvider = ServiceRegister::getService(TimeProviderInterface::class);
        $this->storeId = $data['storeId'];
        $this->paymentLink = new PaymentLink($data['paymentLink']['paymentLinkId'], $data['paymentLink']['merchantReference'], $data['paymentLink']['paymentId'], $data['paymentLink']['expiresAt'] ? $timeProvider->getDateTime($data['paymentLink']['expiresAt']) : null, $data['paymentLink']['redirectionUrl'], $data['paymentLink']['status']);
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $expiresAt = $this->paymentLink->getExpiresAt();
        $data['storeId'] = $this->storeId;
        $data['paymentLink'] = ['paymentLinkId' => $this->paymentLink->getPaymentLinkId(), 'merchantReference' => $this->paymentLink->getMerchantReference(), 'paymentId' => $this->paymentLink->getPaymentId(), 'expiresAt' => $expiresAt ? $expiresAt->getTimestamp() : null, 'redirectionUrl' => $this->paymentLink->getRedirectionUrl(), 'status' => $this->paymentLink->getStatus()];
        return $data;
    }
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    public function getPaymentLink(): PaymentLink
    {
        return $this->paymentLink;
    }
    public function setPaymentLink(PaymentLink $paymentLink): void
    {
        $this->paymentLink = $paymentLink;
    }
    public function getPaymentLinkId(): string
    {
        return $this->paymentLink->getPaymentLinkId();
    }
    public function getMerchantReference(): string
    {
        return $this->paymentLink->getMerchantReference();
    }
}
