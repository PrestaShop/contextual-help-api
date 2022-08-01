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

use Help\PrestaShop\RequestInfo;
use PHPUnit\Framework\TestCase;

class RequestInfoTest extends TestCase
{
    /**
     * @dataProvider isApiProvider
     */
    public function testIsAPI(string $uri, bool $isApi): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($isApi, $requestInfo->isApiRequest());
    }

    /**
     * @dataProvider isProxyProvider
     */
    public function testIsProxy(string $uri, bool $isProxy): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($isProxy, $requestInfo->isProxyRequest());
    }

    /**
     * @dataProvider pathElementsProvider
     *
     * @param string[] $pathElements
     */
    public function testGetPathElements(string $uri, array $pathElements): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($pathElements, $requestInfo->getPathElements());
    }

    /**
     * @dataProvider queriesProvider
     *
     * @param string[] $query
     */
    public function testGetQuery(string $uri, array $query): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($query, $requestInfo->getQuery());
    }

    /**
     * @dataProvider requestsProvider
     *
     * @param string[] $request
     */
    public function testGetRequest(string $uri, array $request): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($request, $requestInfo->getRequest());
    }

    /**
     * @dataProvider versionsProvider
     */
    public function testGetVersion(string $uri, ?string $version): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($version, $requestInfo->getVersion());
    }

    /**
     * @dataProvider languagesProvider
     */
    public function testGetLanguage(string $uri, ?string $language): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($language, $requestInfo->getLanguage());
    }

    /**
     * @dataProvider controllersProvider
     */
    public function testGetController(string $uri, ?string $controller): void
    {
        $requestInfo = RequestInfo::fromRequestUri($uri);
        $this->assertSame($controller, $requestInfo->getController());
    }

    /**
     * @return iterable<array{string, bool}>
     */
    public function isApiProvider(): iterable
    {
        yield ['dfsfsf', false];
        yield ['/api', false];
        yield ['/api?request=', false];
        yield ['/api/?request=', false];
        yield ['/api?request=foo%3Dbar', true];
        yield ['/api/?request=foo%3Dbar', true];
        yield ['/api?request=getHelp%3Dbar', true];
    }

    /**
     * @return iterable<array{string, bool}>
     */
    public function isProxyProvider(): iterable
    {
        yield ['dfsfsf', false];
        yield ['/api', false];
        yield ['/api?request=', false];
        yield ['/api/?request=', false];
        yield ['/api?request=foo%3Dbar', true];
        yield ['/api/?request=foo%3Dbar', true];
        yield ['/api?request=getHelp%3Dbar', false];
    }

    /**
     * @return iterable<array{string, string[]}>
     */
    public function pathElementsProvider(): iterable
    {
        yield ['dfsfsf', ['dfsfsf']];
        yield ['/api', ['api']];
        yield ['/api?request=', ['api']];
        yield ['/api/?request=', ['api']];
        yield ['/api/something/else/', ['api', 'something', 'else']];
    }

    /**
     * @return iterable<array{string, string[]}>
     */
    public function queriesProvider(): iterable
    {
        yield ['/api', []];
        yield ['/api/?request=', ['request' => '']];
        yield ['/api?request=foo%3Dbar', ['request' => 'foo=bar']];
        yield ['?request=foo%3Dbar&version=1.0.0&lang=fr', ['request' => 'foo=bar', 'version' => '1.0.0', 'lang' => 'fr']];
    }

    /**
     * @return iterable<array{string, string[]}>
     */
    public function requestsProvider(): iterable
    {
        yield ['/api', []];
        yield ['/api/?request=', []];
        yield ['/api?request=foo%3Dbar', ['foo' => 'bar']];
        yield ['?request=foo%3Dbar&version=1.0.0&lang=fr', ['foo' => 'bar']];
        yield ['?request=foo%3Dbar%26version%3D1.0.0%26lang%3Dfr', ['foo' => 'bar', 'version' => '1.0.0', 'lang' => 'fr']];
    }

    /**
     * @return iterable<array{string, ?string}>
     */
    public function versionsProvider(): iterable
    {
        yield ['/api', null];
        yield ['/api/?request=', null];
        yield ['/api?request=foo%3Dbar&version=1.0.0&lang=fr', null];
        yield ['/?version=1.0.0&lang=fr', '1.0.0'];
        yield ['/api?request=foo%3Dbar%26version%3D1.0.0%26lang%3Dfr', '1.0.0'];
    }

    /**
     * @return iterable<array{string, ?string}>
     */
    public function languagesProvider(): iterable
    {
        yield ['/api?request=foo%3Dbar&version=1.0.0&language=fr', null];
        yield ['/api?request=foo%3Dbar%26version%3D1.0.0%26lang%3Dfr', null];
        yield ['/?version=1.0.0&language=fr', ''];
        yield ['/en/doc/AdminDashbord?version=1.0.0&language=fr', 'en'];
        yield ['/fr/doc/AdminDashbord?version=1.0.0', 'fr'];
    }

    /**
     * @return iterable<array{string, ?string}>
     */
    public function controllersProvider(): iterable
    {
        yield ['/api?request=foo%3Dbar&version=1.0.0&language=fr', null];
        yield ['/api?request=foo%3Dbar%26version%3D1.0.0%26lang%3Dfr', null];
        yield ['/api?request=getHelp%3DAdminDashbord%26version%3D1.0.0%26lang%3Dfr', 'AdminDashbord'];
        yield ['/?version=1.0.0&language=fr', null];
        yield ['/en/doc/AdminDashbord?version=1.0.0&language=fr', 'AdminDashbord'];
        yield ['/fr/doc/AdminDashbord?version=1.0.0', 'AdminDashbord'];
    }
}
