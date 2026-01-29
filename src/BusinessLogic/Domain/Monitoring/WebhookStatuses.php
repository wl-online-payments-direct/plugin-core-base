<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring;

/**
 * Class WebhookStatuses
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Monitoring
 */
class WebhookStatuses
{
    /** @var string */
    public const COMPLETED = 'COMPLETED';
    /** @var string */
    public const PAID = 'PAID';
    /** @var string */
    public const AWAITING = 'AWAITING';
    /** @var string */
    public const IN_PROGRESS = 'IN_PROGRESS';
    /** @var string */
    public const FAIL = 'FAIL';
    const statusMap = ['CREATED' => self::IN_PROGRESS, 'UNSUCCESSFUL' => self::FAIL, 'PENDING_PAYMENT' => self::IN_PROGRESS, 'PENDING_MERCHANT' => self::AWAITING, 'PENDING_CONNECT_OR_3RD_PARTY' => self::AWAITING, 'REFUNDED' => self::COMPLETED, 'COMPLETED' => self::COMPLETED, 'PAID' => self::COMPLETED];
}
