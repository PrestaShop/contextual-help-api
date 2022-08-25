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

use Help\PrestaShop\ProviderInfo;

interface PresenterInterface
{
    public function canPresentWithProviderInfo(ProviderInfo $providerInfo): bool;

    public function presentContentWithProviderInfo(string $content, ProviderInfo $providerInfo): string;
}
