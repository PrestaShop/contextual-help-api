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
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;

class ImageProcessor
{
    private string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function __invoke(DocumentParsedEvent $event): void
    {
        foreach ($event->getDocument()->iterator() as $image) {
            if (!($image instanceof Image)) {
                continue;
            }
            $image->data->set('attributes', ['class' => 'img-responsive']);
            if (!parse_url($image->getUrl(), PHP_URL_HOST)) {
                $pos = strpos($image->getUrl(), '.gitbook/') ?: 0;
                $url = substr($image->getUrl(), $pos);
                $image->setUrl($this->baseUrl . '/' . $url);
            }
        }
    }
}
