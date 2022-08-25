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
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;

class DescriptionProcessor
{
    public function __invoke(DocumentParsedEvent $event): void
    {
        $firstChild = $event->getDocument()->firstChild();
        if (!$firstChild instanceof Heading || $firstChild->getLevel() !== 1) {
            return;
        }

        /** @var ?array<string, string> $frontMatter */
        $frontMatter = $event->getDocument()->data->get('front_matter');
        if ($frontMatter === null || empty($frontMatter['description'])) {
            return;
        }

        $paragraph = new Paragraph();
        $paragraph->appendChild(new Text($frontMatter['description']));
        $firstChild->insertAfter($paragraph);
    }
}
