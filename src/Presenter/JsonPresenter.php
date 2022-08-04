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

class JsonPresenter extends HtmlPresenter
{
    private string $language;

    public function __construct(
        Environment $twig,
        GithubFlavoredMarkdownConverter $converter,
        HTMLPurifier $purifier,
        string $language
    ) {
        parent::__construct($twig, $converter, $purifier);
        $this->language = $language;
    }

    public function canPresentWithRequestInfo(RequestInfo $requestInfo): bool
    {
        return !empty($requestInfo->getRequest()['getHelp']);
    }

    public function presentContentWithRequestInfo(string $content, RequestInfo $requestInfo): string
    {
        header('Content-Type: application/json');

        $translations = require_once __DIR__ . '/../../config/translations.php';

        if (!empty($requestInfo->getQuery()['callback'])) {
            $html = $this->twig->render('json.html.twig', [
                'body' => $this->prepareBody($content),
                'useful_links' => $translations[$this->language]['useful_links'],
                'prestaShop_Forum' => $translations[$this->language]['prestaShop_Forum'],
                'full_doc' => $translations[$this->language]['full_doc'],
            ]);
            $html = htmlentities($requestInfo->getQuery()['callback'])
                . '(' . json_encode($html) . ');'
                . PHP_EOL;
        }

        $tracker = $this->twig->render('json-ga-tracker.js.twig');

        return ($html ?? '') . $tracker;
    }
}
