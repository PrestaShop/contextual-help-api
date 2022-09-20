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

namespace Help\PrestaShop\Markdown\Renderer;

use Help\PrestaShop\Markdown\Node\Liquid;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

class TabsRendererProcessor implements RendererProcessorInterface
{
    private const HEADING_LEVEL = 6;

    public function process(Node $node): void
    {
        if ($node instanceof Liquid && $node->getType() === 'tab' && key_exists('title', $node->getAttributes())) {
            $heading = new Heading(self::HEADING_LEVEL);
            $heading->appendChild(new Text($node->getAttributes()['title']));
            if ($node->firstChild() === null) {
                $node->appendChild($heading);
            } else {
                $node->firstChild()->insertBefore($heading);
            }
        }
    }
}
