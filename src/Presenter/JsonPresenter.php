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
                'title' => $translations[$this->language]['title'],
                'thanks' => $translations[$this->language]['thanks'],
                'not_at_all_helpful' => $translations[$this->language]['not_at_all_helpful'],
                'not_very_helpful' => $translations[$this->language]['not_very_helpful'],
                'somewhat_helpful' => $translations[$this->language]['somewhat_helpful'],
                'very_helpful' => $translations[$this->language]['very_helpful'],
                'extremely_helpful' => $translations[$this->language]['extremely_helpful'],
                'feedback_reason_1' => $translations[$this->language]['feedback_reason_1'],
                'feedback_reason_2' => $translations[$this->language]['feedback_reason_2'],
                'feedback_reason_3' => $translations[$this->language]['feedback_reason_3'],
                'feedback_reason_4' => $translations[$this->language]['feedback_reason_4'],
                'feedback_reason_5' => $translations[$this->language]['feedback_reason_5'],
                'feedback_reason_6' => $translations[$this->language]['feedback_reason_6'],
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