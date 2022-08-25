<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2022.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Help\PrestaShop;

class ProviderInfo
{
    public const TYPE_HTML = 'html';
    public const TYPE_JSON = 'json';

    private string $version;
    private string $language;
    private string $controller;
    private string $type;
    private ?string $callback;

    public function __construct(RequestInfo $requestInfo, string $version, string $language, string $controller)
    {
        $this->version = $version;
        $this->language = $language;
        $this->controller = $controller;
        $this->type = !empty($requestInfo->getRequest()['getHelp']) ? self::TYPE_JSON : self::TYPE_HTML;
        $this->callback = !empty($requestInfo->getQuery()['callback']) ? $requestInfo->getQuery()['callback'] : null;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCallback(): ?string
    {
        return $this->callback;
    }
}
