<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Help\PrestaShop\Presenter;

use Help\PrestaShop\ProviderInfo;
use HTMLPurifier;
use League\CommonMark\MarkdownConverter;
use Twig\Environment;

class HtmlPresenter implements PresenterInterface
{
    protected Environment $twig;
    protected MarkdownConverter $converter;
    protected HTMLPurifier $purifier;

    public function __construct(Environment $twig, MarkdownConverter $converter, HTMLPurifier $purifier)
    {
        $this->twig = $twig;
        $this->converter = $converter;
        $this->purifier = $purifier;
    }

    public function canPresentWithProviderInfo(ProviderInfo $providerInfo): bool
    {
        return $providerInfo->getType() === ProviderInfo::TYPE_HTML;
    }

    public function presentContentWithProviderInfo(string $content, ProviderInfo $providerInfo): string
    {
        return $this->twig->render('index.html.twig', [
            'body' => $this->prepareBody($content),
        ]);
    }

    protected function prepareBody(string $body): string
    {
        $body = $this->converter->convert($body)->getContent();
        $body = str_replace('****', '', $body); // Gitbook issue with bold links

        $this->purifier->config->set('HTML.Forms', true);
        $this->purifier->config->set('HTML.TargetBlank', true);

        return $this->purifier->purify($body);
    }
}
