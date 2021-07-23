<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Help\PrestaShop\Http;

use Psr\Http\Message\ResponseInterface;

interface AdapterInterface
{
    /**
     * @param string $url
     * @param array<string, array> $options
     *
     * @return ResponseInterface
     */
    public function get(string $url, array $options = []): ResponseInterface;
}
