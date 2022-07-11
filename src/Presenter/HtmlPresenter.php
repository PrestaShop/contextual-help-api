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


use Help\PrestaShop\RequestInfo;
use HTMLPurifier;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Twig\Environment;

class HtmlPresenter implements PresenterInterface
{
    protected Environment $twig;
    protected GithubFlavoredMarkdownConverter $converter;
    protected HTMLPurifier $purifier;

    public function __construct(Environment $twig, GithubFlavoredMarkdownConverter $converter, HTMLPurifier $purifier)
    {
        $this->twig = $twig;
        $this->converter = $converter;
        $this->purifier = $purifier;
    }

    public function canPresentWithRequestInfo(RequestInfo $requestInfo): bool
    {
        return count($requestInfo->getPathElements()) >= 3;
    }

    public function presentContentWithRequestInfo(string $content, RequestInfo $requestInfo): string
    {
        return $this->twig->render('index.html.twig', [
            'body' => $this->prepareBody($content),
        ]);
    }

    protected function prepareBody(string $body): string
    {
        $body = $this->converter->convert($body)->getContent();
        $body = strip_tags($body, '<ul><ol><li><div><p><h1><h2><h3><strong>');
        $body = str_replace('****', '', $body); // Gitbook issue with bold links

        return $this->purifier->purify($body);
    }
}