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
