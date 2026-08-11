<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\AutomaticCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
/**
 * Class JobsSettingsRequest
 *
 * The Global > Jobs page's own slice of the payment settings (V2 UI requirements p9, functional
 * requirements "Global > Jobs Configuration"). Deliberately carries ONLY automatic capture: the
 * fifteen-field `PaymentSettingsRequest` spans three separate V2 pages, so a page that had to send
 * all fifteen would overwrite the slices belonging to Danger Zone and Default Settings with whatever
 * it happened to be holding.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request
 */
class JobsSettingsRequest extends Request
{
    protected int $automaticCapture;
    /**
     * @param int $automaticCapture Delay in minutes before payments are automatically captured, or
     *  -1 for never. Must be one of `AutomaticCapture`'s allowed values.
     */
    public function __construct(int $automaticCapture)
    {
        $this->automaticCapture = $automaticCapture;
    }
    /**
     * @inheritDoc
     *
     * @throws InvalidAutomaticCaptureValueException
     */
    public function transformToDomainModel(): object
    {
        return AutomaticCapture::create($this->automaticCapture);
    }
}
