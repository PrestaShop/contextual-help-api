<?php

namespace Help\PrestaShop\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class GuzzleAdapter implements AdapterInterface
{
    /**
     * @throws GuzzleException
     */
    public function get(string $url, ?array $options = []): ResponseInterface
    {
        return (new Client())->get($url, $options);
    }
}
