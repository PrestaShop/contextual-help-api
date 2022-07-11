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

use Help\PrestaShop\Http\HttpClient;
use Throwable;

class ProxyContentProvider extends DocContentProvider
{
    public function getContentByPageInfos(PageInfos $pageInfos): ?string
    {
        try {
            return $this->httpClient->get(
                str_replace('REQUEST', $pageInfos->getParsedUri()->getQuery()['request'], $this->urlPattern),
                $this->urlOptions
            );
        } catch (Throwable $exception) {
            return null;
        }
    }
}
