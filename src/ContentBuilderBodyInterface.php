<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Help\PrestaShop;

interface ContentBuilderBodyInterface
{
    public function render(PageInfos $infos, mixed $body): string;
}