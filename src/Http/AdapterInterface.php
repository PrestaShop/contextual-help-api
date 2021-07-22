<?php

namespace Help\PrestaShop\Http;

use Psr\Http\Message\ResponseInterface;

interface AdapterInterface
{
    public function get(string $url, ?array $options = []): ResponseInterface;
}
