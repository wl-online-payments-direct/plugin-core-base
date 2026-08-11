<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

/**
 * Class PaymentMethodCollection.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class PaymentMethodCollection
{
    /**
     * @var array<string, PaymentMethod>
     */
    private array $paymentMethods = [];
    /**
     * @param PaymentMethod[] $paymentMethods
     */
    public function __construct(array $paymentMethods = [])
    {
        foreach ($paymentMethods as $paymentMethod) {
            $this->add($paymentMethod);
        }
    }
    public function add(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethods[(string) $paymentMethod->getProductId()] = $paymentMethod;
    }
    /**
     * @param PaymentProductId[] $ids
     * @return void
     */
    public function remove(array $ids): void
    {
        foreach ($ids as $id) {
            unset($this->paymentMethods[(string) $id]);
        }
    }
    public function get(PaymentProductId $id): ?PaymentMethod
    {
        return $this->has($id) ? $this->paymentMethods[(string) $id] : null;
    }
    public function has(PaymentProductId $id): bool
    {
        return array_key_exists((string) $id, $this->paymentMethods);
    }
    public function intersect(PaymentMethodCollection $other): PaymentMethodCollection
    {
        $result = new PaymentMethodCollection();
        foreach ($this->paymentMethods as $paymentMethod) {
            if ($other->has($paymentMethod->getProductId())) {
                $result->add($paymentMethod);
            }
        }
        return $result;
    }
    public function union(PaymentMethodCollection $other): PaymentMethodCollection
    {
        $result = new PaymentMethodCollection($this->paymentMethods);
        foreach ($other->toArray() as $paymentMethod) {
            if (!$this->has($paymentMethod->getProductId())) {
                $result->add($paymentMethod);
            }
        }
        return $result;
    }
    /**
     * Whether a grouped card option is being offered - i.e. whether either card parent is present.
     *
     * Grouping is DERIVED from the method type rather than stored (ADR-0003 decision 12): functional
     * requirements p11 make Embedded Cards and Redirection to Cards "always grouped" and Individual
     * cards never grouped, so no card method carries a merchant-settable flag any more.
     *
     * Note what this no longer means: it does not suppress the Individual card methods. The same page
     * states the three card types have "no dependencies between these 3 types, meaning that all three
     * can be enabled and available on the checkout", so a grouped parent and a standalone Visa button
     * coexist. Under V1's single `cards` method, grouping did suppress the brands.
     */
    public function isCardsGroupingEnabled(): bool
    {
        return $this->has(PaymentProductId::embeddedCards()) || $this->has(PaymentProductId::redirectionToCards());
    }
    /**
     * The embedded (iframe) card flow, which is what hosted tokenization renders. Embedded Cards is
     * always an iframe per functional requirements p11, so its presence IS the answer - there is no
     * stored flow type left to consult.
     */
    public function isCardsTokenizationEnabled(): bool
    {
        return $this->has(PaymentProductId::embeddedCards());
    }
    public function isEmpty(): bool
    {
        return empty($this->paymentMethods);
    }
    /**
     * Sorted ascending by PaymentMethod::getSortOrder(), stable for ties (relies on PHP's usort being
     * stable since 8.0). Use as a final step before presenting a list, since intermediate operations
     * like union()/intersect()/add() do not guarantee the merchant's configured order survives.
     */
    public function sortedByDisplayOrder(): PaymentMethodCollection
    {
        $methods = array_values($this->paymentMethods);
        usort($methods, static function (PaymentMethod $a, PaymentMethod $b): int {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });
        return new PaymentMethodCollection($methods);
    }
    /**
     * @return PaymentMethod[]
     */
    public function toArray(): array
    {
        return $this->paymentMethods;
    }
}
