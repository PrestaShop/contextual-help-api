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

namespace Help\PrestaShop\Presenter;

use Help\PrestaShop\RequestInfo;

class ProxyPresenter extends JsonPresenter
{
    public function canPresentWithRequestInfo(RequestInfo $requestInfo): bool
    {
        return !empty($requestInfo->getQuery()['request']) && empty($requestInfo->getRequest()['getHelp']);
    }

    public function presentContentWithRequestInfo(string $content, RequestInfo $requestInfo): string
    {
        header('Content-Type: application/json');

        if (!empty($requestInfo->getQuery()['callback'])) {
            $encodedContent = htmlentities($requestInfo->getQuery()['callback'])
                . '(' . json_encode($content) . ');'
                . PHP_EOL;
        }

        $tracker = $this->twig->render('json-ga-tracker.js.twig');

        return ($encodedContent ?? '') . $tracker;
    }
}