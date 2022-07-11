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

namespace Help\PrestaShop\Provider;

use GuzzleHttp\Client;
use Help\PrestaShop\RequestInfo;

class GithubProvider implements ProviderInterface
{
    private const ENDPOINT = 'https://raw.githubusercontent.com/PrestaShop/%s/master/%s';
    private const FALLBACK_CONTROLLER = 'GettingStarted';

    private Client $client;
    private string $language;
    private string $repository;
    private array $mapping;

    public function __construct(Client $client, string $language, string $repository, array $mapping)
    {
        $this->client = $client;
        $this->language = $language;
        $this->repository = $repository;
        $this->mapping = $mapping;
    }

    public function getContentFromRequestInfo(RequestInfo $requestInfo): string
    {
        return $this->client->get($this->getUrlFromRequestInfo($requestInfo))->getBody()->getContents();
    }

    private function getUrlFromRequestInfo(RequestInfo $requestInfo): string
    {
        $controller = $requestInfo->getController();

        if (!isset($this->mapping[$controller])) {
            $controller = self::FALLBACK_CONTROLLER;
        }

        $page = $this->mapping[$controller][$this->language];

        return sprintf(self::ENDPOINT, $this->repository, $page);
    }
}