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

use Help\PrestaShop\Markdown\Node\Liquid;
use Help\PrestaShop\Markdown\Parser\Block\LiquidBlockStartParser;
use Help\PrestaShop\Markdown\Parser\Inline\EmojiParser;
use Help\PrestaShop\Markdown\Renderer\HintRendererProcessor;
use Help\PrestaShop\Markdown\Renderer\LiquidRenderer;
use Help\PrestaShop\Markdown\Renderer\TabsRendererProcessor;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class GitbookExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('gitbook', Expect::structure([
            'relative_link_url' => Expect::anyOf(Expect::string(), Expect::null()),
            'relative_img_url' => Expect::string(),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $relativeImgUrl = $environment->getConfiguration()->get('gitbook/relative_img_url');
        $relativeLinkUrl = $environment->getConfiguration()->get('gitbook/relative_link_url');

        $environment->addBlockStartParser(new LiquidBlockStartParser());
        $environment->addInlineParser(new EmojiParser());
        $environment->addEventListener(DocumentParsedEvent::class, new DescriptionProcessor());
        $environment->addEventListener(
            DocumentParsedEvent::class,
            new ImageProcessor(is_string($relativeImgUrl) ? $relativeImgUrl : ''),
        );
        $environment->addEventListener(
            DocumentParsedEvent::class,
            new LinkProcessor(is_string($relativeLinkUrl) ? $relativeLinkUrl : null),
        );
        $environment->addRenderer(Liquid::class, new LiquidRenderer(new HintRendererProcessor(), new TabsRendererProcessor()));
    }
}
