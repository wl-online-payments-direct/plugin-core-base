<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Http;

use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
/**
 * Class LoggingHttpClient.
 *
 * @package OnlinePayments\Core\Infrastructure\Http
 */
class LoggingHttpClient extends HttpClient
{
    /**
     * @var HttpClient
     */
    private HttpClient $client;
    /**
     * LoggingHttpclient constructor.
     *
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }
    /**
     * @inheritdoc Create, log and send request.
     */
    public function request(string $method, string $url, ?array $headers = [], string $body = ''): HttpResponse
    {
        Logger::logDebug("Sending http request to {$url}", 'Core', ['Type' => $method, 'Endpoint' => $url, 'Headers' => json_encode($headers), 'Content' => $body]);
        $response = $this->client->request($method, $url, $headers, $body);
        Logger::logDebug("Http response from {$url}", 'Core', ['ResponseFor' => "{$method} at {$url}", 'Status' => $response->getStatus(), 'Headers' => json_encode($response->getHeaders()), 'Content' => $response->getBody()]);
        return $response;
    }
    /**
     * @inheritdoc Create, log and send request asynchronously.
     */
    public function requestAsync(string $method, string $url, ?array $headers = [], string $body = '1'): void
    {
        Logger::logDebug("Sending async http request to {$url}", 'Core', ['Type' => $method, 'Endpoint' => $url, 'Headers' => json_encode($headers), 'Content' => $body]);
        $this->client->requestAsync($method, $url, $headers, $body);
    }
    /**
     * @inheritdoc
     */
    protected function sendHttpRequest(string $method, string $url, ?array $headers = [], string $body = ''): HttpResponse
    {
        return $this->client->sendHttpRequest($method, $url, $headers, $body);
    }
    /**
     * @inheritdoc
     */
    protected function sendHttpRequestAsync(string $method, string $url, ?array $headers = [], string $body = ''): void
    {
        $this->client->sendHttpRequestAsync($method, $url, $headers, $body);
    }
}
