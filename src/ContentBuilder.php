<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Help\PrestaShop;

use HTMLPurifier;
use HTMLPurifier_Config;
use Monolog\Logger;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ContentBuilder
{
    public function __construct(
        private ContentProviderInterface $docContentProvider,
        private PageInfosBuilder $pageInfosBuilder,
        private Environment $twig,
        private Logger $logger
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getContent(string $uri, string | null $version): string
    {
        try {
            $pageInfos = $this->pageInfosBuilder->getPageInfosFromUri($uri, $version);

            $streamContent = $this->docContentProvider->getContentByPageId($pageInfos->getPageId());

            if (!is_string($streamContent)) {
                throw new \Exception('Cannot retrieve doc content');
            }

            $body = $this->buildBody(json_decode($streamContent));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return $this->twig->render('error.html.twig');
        }

        return $this->twig->render('index.html.twig', [
            'body' => $body,
            'pageInfos' => $pageInfos,
        ]);
    }

    /**
     * @param mixed $content
     */
    private function buildBody(mixed $content): string
    {
        $content = (string) $content->body->view->value;

        // Clean the html content
        $content = str_replace("\n", '', $content);
        $content = preg_replace('@<ac:macro\s+ac:name=\"(\w+)\">@', '<div class="$1">', $content);
        $content = preg_replace('@</ac:macro>@', '</div>', (string) $content);
        $content = preg_replace_callback('/<!\[CDATA\[(.*)\]\]>/U', function ($matches) {
            return htmlspecialchars($matches[1]);
        }, (string) $content);
        $content = strip_tags((string) $content, '<ul><ol><li><div><p><h1><h2><h3><strong>');
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Attr.EnableID', true);
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($content);
    }
}
