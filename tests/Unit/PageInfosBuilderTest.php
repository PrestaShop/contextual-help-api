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

use Help\PrestaShop\PageInfosBuilder;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class PageInfosBuilderTest extends TestCase
{
    private PageInfosBuilder $pageInfosBuilder;
    /**
     * @var array<string, array>
     */
    private array $mapping16;
    /**
     * @var array<string, array>
     */
    private array $mapping17;

    protected function setUp(): void
    {
        $logger = $this->createMock(Logger::class);

        $this->pageInfosBuilder = new PageInfosBuilder(MAPPING_FILES_PATH, $logger);
        $this->mapping16 = include dirname(__DIR__, 2) . '/config/mappings/mapping16.php';
        $this->mapping17 = include dirname(__DIR__, 2) . '/config/mappings/mapping17.php';
    }

    public function testGetPageInfosFromUri(): void
    {
        $infos = $this->pageInfosBuilder->getPageInfosFromUri('/en/doc/AdminDashboard?version=1.7.8.0', '1.7.8.0');

        $this->assertSame($this->mapping17['AdminDashboard']['en'], $infos->getPageId());
        $this->assertSame('AdminDashboard', $infos->getController());
        $this->assertSame('en', $infos->getLanguage());
    }

    public function testGetPageInfosFromUriTakesFallbackVersionWhenVersionIsMissing(): void
    {
        $infos = $this->pageInfosBuilder->getPageInfosFromUri('/en/doc/AdminDashboard', null);

        $this->assertSame($this->mapping16['AdminDashboard']['en'], $infos->getPageId());
        $this->assertSame('AdminDashboard', $infos->getController());
        $this->assertSame('en', $infos->getLanguage());
    }

    public function testGetPageInfosFromUriTakesFallbackVersionWhenVersionLowerThanFallback(): void
    {
        $infos = $this->pageInfosBuilder->getPageInfosFromUri('/en/doc/AdminDashboard', '1.5.4');

        $this->assertSame($this->mapping16['AdminDashboard']['en'], $infos->getPageId());
        $this->assertSame('AdminDashboard', $infos->getController());
        $this->assertSame('en', $infos->getLanguage());
    }

    public function testGetPageInfosFromUriTakesFallbackVersionWhenVersionHasNoMapping(): void
    {
        $infos = $this->pageInfosBuilder->getPageInfosFromUri('/en/doc/AdminDashboard', '1.8.4');

        $this->assertSame($this->mapping16['AdminDashboard']['en'], $infos->getPageId());
        $this->assertSame('AdminDashboard', $infos->getController());
        $this->assertSame('en', $infos->getLanguage());
    }

    public function testGetPageInfosFromUriReturnsDefaultHelpWhenUriIsMalformed(): void
    {
        $infos = $this->pageInfosBuilder->getPageInfosFromUri('/AdminDashboard/doc/en?version=1.7.8.0', '1.7.8.0');

        $this->assertSame($this->mapping17['GettingStarted']['en'], $infos->getPageId());
        $this->assertSame('GettingStarted', $infos->getController());
        $this->assertSame('en', $infos->getLanguage());
    }

    public function testGetPageInfosFromUriReturnsDefaultLanguageHelpWhenLanguageIsUnknown(): void
    {
        $infos = $this->pageInfosBuilder->getPageInfosFromUri('/kw/doc/AdminDashboard?version=1.7.8.0', '1.7.8.0');

        $this->assertSame($this->mapping17['AdminDashboard']['en'], $infos->getPageId());
        $this->assertSame('AdminDashboard', $infos->getController());
        $this->assertSame('en', $infos->getLanguage());
    }

    public function testGetPageInfosFromUriReturnsDefaultControllerHelpWhenControllerIsUnknown(): void
    {
        $infos = $this->pageInfosBuilder->getPageInfosFromUri('/fr/doc/FakeController?version=1.7.8.0', '1.7.8.0');

        $this->assertSame($this->mapping17['GettingStarted']['fr'], $infos->getPageId());
        $this->assertSame('GettingStarted', $infos->getController());
        $this->assertSame('fr', $infos->getLanguage());
    }

    public function testGetPageInfosFromUriFailsWhenUriIsMissingParams(): void
    {
        $this->expectExceptionMessage('Cannot load page infos');
        $this->pageInfosBuilder->getPageInfosFromUri('/en/AdminDashboard?version=1.7.8.0', '1.7.8.0');
    }
}
