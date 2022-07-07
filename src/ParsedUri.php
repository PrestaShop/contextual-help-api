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

namespace Help\PrestaShop;

use Exception;

class ParsedUri
{
    private array $pathElements;
    private array $query;
    private array $request;

    public function __construct(string $uri)
    {
        $urlComponents = parse_url($uri);
        $pathElements = explode('/', trim($urlComponents['path'] ?? '', '/'));
        parse_str($urlComponents['query'] ?? '', $query);
        parse_str($query['request'] ?? '', $request);

        if (count($pathElements) < 3 && (current($pathElements) !== 'api' || empty($request))) {
            throw new Exception('Invalid URL' . $uri);
        }

        $this->pathElements = $pathElements;
        $this->query = is_array($query) ? $query : [];
        $this->request = is_array($request) ? $request : [];
    }

    public function isApiRequest(): bool
    {
        return reset($this->pathElements) === 'api' && !empty($this->request);
    }

    public function getPathElements(): array
    {
        return $this->pathElements;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getRequest(): array
    {
        return $this->request;
    }
}