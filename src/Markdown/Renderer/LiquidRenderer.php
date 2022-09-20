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
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class LiquidRenderer implements NodeRendererInterface
{
    /** @var RendererProcessorInterface[] */
    private array $processors;

    public function __construct(RendererProcessorInterface ...$processors)
    {
        $this->processors = $processors;
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (!($node instanceof Liquid)) {
            return null;
        }

        foreach ($this->processors as $processor) {
            $processor->process($node);
        }

        $attrs = $node->getAttributes();
        $attrs['class'] = $node->getType() . (!empty($attrs['class']) ? ' ' . $attrs['class'] : '');

        $filling = $childRenderer->renderNodes($node->children());
        $innerSeparator = $childRenderer->getInnerSeparator();

        return new HtmlElement(
            'div',
            $attrs,
            $innerSeparator . $filling . $innerSeparator
        );
    }
}
