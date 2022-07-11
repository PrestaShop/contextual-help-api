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

use HTMLPurifier;
use HTMLPurifier_Config;
use Twig\Environment;

class ProxyContentBuilderBody implements ContentBuilderBodyInterface
{
    public function __construct(private Environment $twig) {}

    public function render(PageInfos $infos, mixed $body): string
    {
        header('Content-Type: application/json');

        if ($infos->getCallback() !== null) {
            $content = htmlentities($infos->getCallback()) . '(' . json_encode($body) . ');' . PHP_EOL;
        }

        return ($content ?? '') . $this->twig->render('json-ga-tracker.js.twig');
    }
}