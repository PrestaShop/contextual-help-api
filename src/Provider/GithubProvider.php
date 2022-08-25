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
use Help\PrestaShop\ProviderInfo;

class GithubProvider implements ProviderInterface
{
    private const ENDPOINT = 'https://raw.githubusercontent.com/PrestaShop/%s/master/%s';

    private Client $client;
    private string $repository;
    /** @var string[][] */
    private array $mapping;

    /**
     * @param Client $client
     * @param string $repository
     * @param string[][] $mapping
     */
    public function __construct(Client $client, string $repository, array $mapping)
    {
        $this->client = $client;
        $this->repository = $repository;
        $this->mapping = $mapping;
    }

    public function getContentFromProviderInfo(ProviderInfo $providerInfo): string
    {
        return $this->client->get($this->getUrlFromProviderInfo($providerInfo))->getBody()->getContents();
    }

    private function getUrlFromProviderInfo(ProviderInfo $providerInfo): string
    {
        $page = $this->mapping[$providerInfo->getController()][$providerInfo->getLanguage()];

        return sprintf(self::ENDPOINT, $this->repository, $page);
    }
}
