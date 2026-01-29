<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\ApiFacades\CaptureAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Capture\CaptureResponse;
/**
 * Class CreateCaptureResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\CaptureAPI\Response
 */
class CreateCaptureResponse extends Response
{
    private CaptureResponse $response;
    /**
     * @param CaptureResponse $response
     */
    public function __construct(CaptureResponse $response)
    {
        $this->response = $response;
    }
    public function getResponse(): CaptureResponse
    {
        return $this->response;
    }
    public function toArray(): array
    {
        return ['id' => $this->response->getId(), 'status' => $this->response->getStatus(), 'statusCode' => $this->response->getStatusCode()->getCode()];
    }
}
