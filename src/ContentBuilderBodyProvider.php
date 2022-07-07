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

use Twig\Environment;

class ContentBuilderBodyProvider
{
    public function __construct(private Environment $twig, private string $translationsFile) {}

    public function getContentBuilderBody(PageInfos $infos): ContentBuilderBodyInterface
    {
        if ($infos->getPageType() === PageInfos::PAGE_TYPE_JSON) {
            return new JsonContentBuilderBody($this->twig, $this->translationsFile);
        }

        return new HtmlContentBuilderBody($this->twig);
    }
}