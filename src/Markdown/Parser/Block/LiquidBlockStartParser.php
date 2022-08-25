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

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

class LiquidBlockStartParser implements BlockStartParserInterface
{
    public const PATTERN = '/^{%\s*(.*?)(?:\s(.*?)\s?)?\s*%}/i';

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if (preg_match(self::PATTERN, $cursor->getLine(), $matches)) {
            $cursor->advanceBy(strlen($matches[0]));
            $cursor->advanceToNextNonSpaceOrNewline();

            return BlockStart::of(new LiquidBlockParser($matches[0]))->at($cursor);
        }

        return BlockStart::none();
    }
}
