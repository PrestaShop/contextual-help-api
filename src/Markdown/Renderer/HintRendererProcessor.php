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

class HintRendererProcessor implements RendererProcessorInterface
{
    public function process(Node $node): void
    {
        if ($node instanceof Liquid && $node->getType() === 'hint' && key_exists('style', $node->getAttributes())) {
            $attributes = $node->getAttributes();
            $attributes['class'] = $attributes['style'] . (!empty($attributes['class']) ? ' ' . $attributes['class'] : '');
            $node->setAttributes($attributes);
        }
    }
}
