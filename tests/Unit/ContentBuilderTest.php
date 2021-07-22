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

namespace Tests\Unit;

use Help\PrestaShop\ContentBuilder;
use Help\PrestaShop\ContentProviderInterface;
use Help\PrestaShop\PageInfos;
use Help\PrestaShop\PageInfosBuilder;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ContentBuilderTest extends TestCase
{
    private ContentBuilder $contentBuilder;
    private Environment $twig;
    private string $body;

    protected function setUp(): void
    {
        $this->body = '<p><strong>Contenu</strong></p>';
        $content = '{
            "id": "20840506",
            "type": "page",
            "status": "current",
            "title": "Découvrir la zone d\'administration",
            "body": {
                "view": {
                    "value": "' . $this->body . '",
                    "representation": "storage",
                    "_expandable": {
                       "webresource": "",
                       "content": "/rest/api/content/20840506"
                    }
                }
            }
        }';

        $docContentProvider = $this->createMock(ContentProviderInterface::class);
        $docContentProvider->method('getContentByPageId')->willReturn($content);

        $templatesLoader = new FilesystemLoader(TEMPLATES_PATH);
        $this->twig = new Environment($templatesLoader);

        $logger = $this->createMock(Logger::class);

        $pageInfosBuilder = new PageInfosBuilder(MAPPING_FILES_PATH, $logger);
        $this->contentBuilder = new ContentBuilder($docContentProvider, $pageInfosBuilder, $this->twig, $logger);
    }

    public function testGetContent(): void
    {
        $content = $this->contentBuilder->getContent('/en/doc/AdminDashboard?version=1.7.8.0', '1.7.8.0');

        $this->assertSame($this->renderSuccess('AdminDashboard', 'en'), $content);
    }

    public function testGetContentReturnsErrorTemplateWhenExceptionIsThrown(): void
    {
        $content = $this->contentBuilder->getContent('/en/AdminDashboard?version=1.7.8.0', '1.7.8.0');

        $this->assertSame($this->renderError(), $content);
    }

    private function renderSuccess(string $controller, string $language): string
    {
        return $this->twig->render('index.html.twig', [
            'body' => $this->body,
            'pageInfos' => new PageInfos(999999, $controller, $language), // ID not really important here
        ]);
    }

    private function renderError(): string
    {
        return $this->twig->render('error.html.twig');
    }
}
