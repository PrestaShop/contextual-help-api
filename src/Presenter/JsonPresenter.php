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

use Help\PrestaShop\ProviderInfo;

class JsonPresenter extends HtmlPresenter
{
    public function canPresentWithProviderInfo(ProviderInfo $providerInfo): bool
    {
        return $providerInfo->getType() === ProviderInfo::TYPE_JSON;
    }

    public function presentContentWithProviderInfo(string $content, ProviderInfo $providerInfo): string
    {
        header('Content-Type: application/json');

        $translations = require_once __DIR__ . '/../../config/translations.php';

        if ($providerInfo->getCallback()) {
            $html = $this->twig->render('json.html.twig', [
                'body' => $this->prepareBody($content),
                'useful_links' => $translations[$providerInfo->getLanguage()]['useful_links'],
                'prestaShop_Forum' => $translations[$providerInfo->getLanguage()]['prestaShop_Forum'],
                'full_doc' => $translations[$providerInfo->getLanguage()]['full_doc'],
            ]);
            $html = htmlentities($providerInfo->getCallback())
                . '(' . json_encode($html) . ');'
                . PHP_EOL;
        }

        $tracker = $this->twig->render('json-ga-tracker.js.twig');

        return ($html ?? '') . $tracker;
    }
}
