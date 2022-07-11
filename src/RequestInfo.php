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

class RequestInfo
{
    private array $pathElements;
    private array $query;
    private array $request;

    private function __construct(string $uri)
    {
        $urlComponents = parse_url($uri);
        $pathElements = explode('/', trim($urlComponents['path'] ?? '', '/'));
        parse_str($urlComponents['query'] ?? '', $query);
        parse_str($query['request'] ?? '', $request);

        $this->pathElements = $pathElements;
        $this->query = is_array($query) ? $query : [];
        $this->request = is_array($request) ? $request : [];
    }

    public static function fromRequestUri(string $uri): self
    {
        return new self($uri);
    }

    public function isApiRequest(): bool
    {
        return reset($this->pathElements) === 'api' && !empty($this->request);
    }

    public function isProxyRequest(): bool
    {
        return $this->isApiRequest() && empty($this->request['getHelp']);
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

    public function getVersion(): ?string
    {
        return $this->isApiRequest()
            ? ($this->getRequest()['version'] ?? null)
            : ($this->getQuery()['version'] ?? null)
        ;
    }

    public function getLanguage(): ?string
    {
        return $this->isApiRequest()
            ? ($this->getRequest()['language'] ?? null)
            : $this->getPathElements()[0]
        ;
    }

    public function getController(): ?string
    {
        if ($this->isApiRequest()) {
            if ($this->isProxyRequest()) {
                return null;
            }
            return $this->getRequest()['getHelp'];
        }

        return $this->getPathElements()[2] ?? null;
    }
}