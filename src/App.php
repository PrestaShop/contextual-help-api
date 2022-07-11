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
    private RequestInfo $requestInfo;
    private GithubProvider $githubProvider;
    private PresenterBuilder $presenterBuilder;

    public function __construct(RequestInfo $requestInfo, GithubProvider $githubProvider, PresenterBuilder $presenterBuilder)
    {
        $this->requestInfo = $requestInfo;
        $this->githubProvider = $githubProvider;
        $this->presenterBuilder = $presenterBuilder;
    }

    public function run(): void
    {
        $content = $this->githubProvider->getContentFromRequestInfo($this->requestInfo);

        echo $this->presenterBuilder
            ->getPresenter($this->requestInfo)
            ->presentContentWithRequestInfo($content, $this->requestInfo)
        ;
    }
}