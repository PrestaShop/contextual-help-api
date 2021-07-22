<?php

namespace Help\PrestaShop\Http;

use HttpRequestException;

class HttpClient
{
    private const HTTP_OK = 200;

    public function __construct(private AdapterInterface $adapter)
    {
    }

    /**
     * @throws HttpRequestException
     */
    public function get(string $url, ?array $options = []): string
    {
        try {
            $response = $this->adapter->get($url, $options);
        } catch (\Throwable $exception) {
            throw new HttpRequestException();
        }

        if (self::HTTP_OK !== $response->getStatusCode()) {
            throw new HttpRequestException();
        }

        return $response->getBody()->getContents();
    }
}
