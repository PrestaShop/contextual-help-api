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

use Help\PrestaShop\Presenter\PresenterBuilder;
use Help\PrestaShop\Provider\GithubProvider;

class App
{
    private ProviderInfo $providerInfo;
    private GithubProvider $githubProvider;
    private PresenterBuilder $presenterBuilder;

    public function __construct(ProviderInfo $providerInfo, GithubProvider $githubProvider, PresenterBuilder $presenterBuilder)
    {
        $this->providerInfo = $providerInfo;
        $this->githubProvider = $githubProvider;
        $this->presenterBuilder = $presenterBuilder;
    }

    public function run(): string
    {
        $content = $this->githubProvider->getContentFromProviderInfo($this->providerInfo);

        return $this->presenterBuilder
            ->getPresenter($this->providerInfo)
            ->presentContentWithProviderInfo($content, $this->providerInfo)
        ;
    }
}
