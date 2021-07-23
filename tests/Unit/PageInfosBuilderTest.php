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
