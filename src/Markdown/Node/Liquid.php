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

namespace Help\PrestaShop\Markdown\Node;

use League\CommonMark\Node\Block\AbstractBlock;

class Liquid extends AbstractBlock
{
    private string $type;

    /**
     * @var array<string, string>
     */
    private array $attributes;

    /**
     * @param array<string, string> $attributes
     */
    public function __construct(string $type, array $attributes = [])
    {
        parent::__construct();
        $this->type = $type;
        $this->attributes = $attributes;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, string> $attributes
     *
     * @return void
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
