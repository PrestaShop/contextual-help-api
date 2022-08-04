<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Help\PrestaShop\Presenter;

use Help\PrestaShop\RequestInfo;

interface PresenterInterface
{
    public function canPresentWithRequestInfo(RequestInfo $requestInfo): bool;

    public function presentContentWithRequestInfo(string $content, RequestInfo $requestInfo): string;
}
