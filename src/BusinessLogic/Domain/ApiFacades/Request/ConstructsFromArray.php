<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionParameter;
/**
 * Builds a request DTO from a keyed array instead of a long positional argument list.
 *
 * WHY THIS EXISTS. `PaymentSettingsRequest` takes 22 constructor parameters and
 * `PaymentMethodRequest` takes 23, and `composer.json` pins `php >= 7.4`, so there are no named
 * arguments to label them at the call site. Long positional lists fail in a specific, nasty way: a
 * run of same-typed parameters can be transposed with no type error and no exception, and the wrong
 * value is simply persisted. Two runs in `PaymentSettingsRequest` are live examples:
 *
 *   * positions 21-22, `templateIdHostedCheckout` and `templateIdEmbeddedCheckout`, both `string` -
 *     swap them and each checkout flow silently renders with the other's template;
 *   * positions 16-18, `enable3ds` / `enforceStrongAuthentication` / `enable3dsExemption`, a
 *     three-long run of `?bool` - swap the last two and "always challenge" quietly becomes "exempt
 *     where possible", which is a change to the store's authentication posture.
 *
 * Neither is caught by types, tests of the constructor, or review of the constructor. They are caught
 * by not writing the call that way.
 *
 * WHAT IT DOES NOT DO. The constructors stay exactly as they are. Platform integrations
 * (PrestaShop, Shopware, OpenCart) construct these DTOs from outside this repository, and that work
 * is deferred - reordering or removing parameters would break repositories not visible from here.
 * So this is purely additive: Core builds its requests through `fromArray()`, and the positional
 * constructor remains the platform-facing contract until those integrations can be migrated
 * deliberately.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request
 */
trait ConstructsFromArray
{
    /**
     * @param array<string, mixed> $data Keys are constructor parameter names. Order is irrelevant.
     *     Optional parameters may be omitted and fall back to the constructor's own defaults, so the
     *     defaults are never restated here and cannot drift from them.
     *
     * @return static
     *
     * @throws InvalidArgumentException If a key does not name a constructor parameter, or a parameter
     *     with no default is missing.
     */
    public static function fromArray(array $data): self
    {
        $constructor = (new ReflectionClass(static::class))->getConstructor();
        if (null === $constructor) {
            return new static();
        }
        $parameters = $constructor->getParameters();
        // An unrecognised key is rejected rather than ignored. Ignoring it would reintroduce exactly
        // the failure mode this trait exists to remove: a misspelled `templateIdHostedChekout` would
        // be dropped in silence and the field would take its default, with nothing to notice.
        $known = array_map(static function (ReflectionParameter $parameter): string {
            return $parameter->getName();
        }, $parameters);
        $unknown = array_diff(array_keys($data), $known);
        if (!empty($unknown)) {
            throw new InvalidArgumentException(sprintf('%s::fromArray() received unknown key(s): %s. Known keys: %s.', static::class, implode(', ', $unknown), implode(', ', $known)));
        }
        $arguments = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $data)) {
                $arguments[] = $data[$name];
                continue;
            }
            if (!$parameter->isDefaultValueAvailable()) {
                throw new InvalidArgumentException(sprintf('%s::fromArray() is missing required key "%s".', static::class, $name));
            }
            $arguments[] = $parameter->getDefaultValue();
        }
        return new static(...$arguments);
    }
}
