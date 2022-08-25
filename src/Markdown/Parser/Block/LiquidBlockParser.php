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

namespace Help\PrestaShop\Markdown\Parser\Block;

use Help\PrestaShop\Markdown\Node\Liquid;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

class LiquidBlockParser implements BlockContinueParserInterface
{
    private const END_PATTERN = '/^{%%\s?%s\s?%%}/i';
    private const ATTRIBUTES_PATTERN = '/\s*(.*?)\s?=\s?"((?:[^"\\\]|\\.)*?)"/i';

    private Liquid $liquid;
    private string $startBlock;
    private string $endBlock;

    public function __construct(string $startBlock)
    {
        $this->startBlock = $startBlock;
        preg_match(LiquidBlockStartParser::PATTERN, $this->startBlock, $matches);
        $this->endBlock = 'end' . $matches[1];
        $this->liquid = new Liquid($matches[1], $this->getAttributes($matches[2]));
    }

    public function getBlock(): AbstractBlock
    {
        return $this->liquid;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canHaveLazyContinuationLines(): bool
    {
        return false;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->match(sprintf(self::END_PATTERN, preg_quote($this->endBlock))) !== null) {
            return BlockContinue::finished();
        }

        return BlockContinue::at($cursor);
    }

    public function addLine(string $line): void
    {
    }

    public function closeBlock(): void
    {
    }

    /**
     * @param string $attributes
     *
     * @return array<string, string>
     */
    private function getAttributes(string $attributes): array
    {
        if (empty($attributes)) {
            return [];
        }

        $attrs = [];
        preg_match_all(self::ATTRIBUTES_PATTERN, $attributes, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            array_shift($match);
            $attrs[(string) $match[0]] = (string) $match[1];
        }

        return $attrs;
    }
}
