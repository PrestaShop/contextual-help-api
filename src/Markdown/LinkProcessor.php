<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2022.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Help\PrestaShop\Markdown;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;

class LinkProcessor
{
    private ?string $url;

    public function __construct(?string $url)
    {
        $this->url = $url;
    }

    public function __invoke(DocumentParsedEvent $event): void
    {
        foreach ($event->getDocument()->iterator() as $link) {
            if (!($link instanceof Link) || $link->firstChild() === null) {
                continue;
            }

            $link->data->set('attributes', ['target' => '_blank']);

            if (!parse_url($link->getUrl(), PHP_URL_HOST)) {
                if ($this->url === null) {
                    $link->replaceWith($link->firstChild());
                } else {
                    $link->setUrl($this->url . '/' . preg_replace('/\.md/i', '', $link->getUrl()));
                }
            }
        }
    }
}
