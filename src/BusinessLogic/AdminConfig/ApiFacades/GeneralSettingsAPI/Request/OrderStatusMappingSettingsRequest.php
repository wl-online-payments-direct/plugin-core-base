<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping\Models\OrderStatusMapping;
/**
 * Class OrderStatusMappingSettingsRequest
 *
 * The Global > Danger Zone page's own slice of the payment settings (V2 UI requirements p9,
 * functional requirements "Global > Danger Zone Configuration"). Deliberately carries ONLY the seven
 * order-status mappings: the fifteen-field `PaymentSettingsRequest` spans three separate V2 pages, so
 * a page that had to send all fifteen would overwrite the slices belonging to Jobs and Default
 * Settings with whatever it happened to be holding.
 *
 * Transforms to the existing `OrderStatusMapping` domain model rather than a new one - it already
 * describes exactly these seven fields, and `StoreService::getDefaultOrderStatusMapping()` already
 * produces it.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request
 */
class OrderStatusMappingSettingsRequest extends Request
{
    protected string $paymentCapturedStatus;
    protected string $paymentErrorStatus;
    protected string $paymentPendingStatus;
    protected string $paymentAuthorizedStatus;
    protected string $paymentCancelledStatus;
    protected string $paymentRefundedStatus;
    protected string $paymentPartiallyRefundedStatus;
    /**
     * @param string $paymentCapturedStatus
     * @param string $paymentErrorStatus
     * @param string $paymentPendingStatus
     * @param string $paymentAuthorizedStatus
     * @param string $paymentCancelledStatus
     * @param string $paymentRefundedStatus
     * @param string $paymentPartiallyRefundedStatus Optional: not every shop system exposes a
     *  partially-refunded status (functional requirements p6 notes the available mappings differ per
     *  system).
     */
    public function __construct(string $paymentCapturedStatus, string $paymentErrorStatus, string $paymentPendingStatus, string $paymentAuthorizedStatus, string $paymentCancelledStatus, string $paymentRefundedStatus, string $paymentPartiallyRefundedStatus = '')
    {
        $this->paymentCapturedStatus = $paymentCapturedStatus;
        $this->paymentErrorStatus = $paymentErrorStatus;
        $this->paymentPendingStatus = $paymentPendingStatus;
        $this->paymentAuthorizedStatus = $paymentAuthorizedStatus;
        $this->paymentCancelledStatus = $paymentCancelledStatus;
        $this->paymentRefundedStatus = $paymentRefundedStatus;
        $this->paymentPartiallyRefundedStatus = $paymentPartiallyRefundedStatus;
    }
    /**
     * @inheritDoc
     */
    public function transformToDomainModel(): object
    {
        return new OrderStatusMapping($this->paymentCapturedStatus, $this->paymentErrorStatus, $this->paymentPendingStatus, $this->paymentAuthorizedStatus, $this->paymentCancelledStatus, $this->paymentRefundedStatus, $this->paymentPartiallyRefundedStatus);
    }
}
