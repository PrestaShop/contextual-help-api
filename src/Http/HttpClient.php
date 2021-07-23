<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Help\PrestaShop\Http;

use HttpRequestException;

class HttpClient
{
    private const HTTP_OK = 200;

    public function __construct(private AdapterInterface $adapter)
    {
    }

    /**
     * @param string $url
     * @param array<string, array> $options
     *
     * @return string
     *
     * @throws HttpRequestException
     */
    public function get(string $url, array $options = []): string
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
