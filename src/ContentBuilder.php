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

namespace Help\PrestaShop;

use Monolog\Logger;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ContentBuilder
{
    public function __construct(
        private ContentProviderInterface $docContentProvider,
        private ContentBuilderBodyProvider $contentBuilderBodyProvider,
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
    public function getContent(string $uri, ?string $version): string
    {
        try {
            $pageInfos = $this->pageInfosBuilder->getPageInfosFromUri($uri, $version);

            $streamContent = $this->docContentProvider->getContentByPageId($pageInfos->getPageId());

            if (!is_string($streamContent)) {
                throw new \Exception('Cannot retrieve doc content');
            }

            $bodyBuilder = $this->contentBuilderBodyProvider->getContentBuilderBody($pageInfos);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return $this->twig->render('error.html.twig');
        }

        return $bodyBuilder->render($pageInfos, json_decode($streamContent));
    }
}
