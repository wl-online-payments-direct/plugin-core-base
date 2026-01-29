<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Http;

/**
 * Class HttpResponse.
 *
 * @package OnlinePayments\Core\Infrastructure\Http
 */
class HttpResponse
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * HTTP status.
     *
     * @var int
     */
    private int $status;
    /**
     * Response body.
     *
     * @var string
     */
    private string $body;
    /**
     * HTTP headers.
     *
     * @var array
     */
    private array $headers;
    /**
     * HttpResponse constructor.
     *
     * @param int $status HTTP status
     * @param array $headers HTTPS headers
     * @param string $body Response body
     */
    public function __construct(int $status, array $headers, string $body)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }
    /**
     * Returns response status.
     *
     * @return int HTTPS status.
     */
    public function getStatus(): int
    {
        return $this->status;
    }
    /**
     * Returns response body.
     *
     * @return string Response body.
     */
    public function getBody(): string
    {
        return $this->body;
    }
    /**
     * Returns json decoded response body.
     *
     * @return array Response body decoded as json decode.
     */
    public function decodeBodyToArray(): array
    {
        $result = json_decode($this->body, \true);
        return !empty($result) ? $result : [];
    }
    /**
     * Return. response headers.
     *
     * @return array Array of HTTP headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
    /**
     * Verifies HTTP status code.
     *
     * @return bool Returns TRUE if in success range [200, 300); otherwise, FALSE.
     */
    public function isSuccessful(): bool
    {
        return $this->status !== null && $this->getStatus() >= 200 && $this->getStatus() < 300;
    }
}
