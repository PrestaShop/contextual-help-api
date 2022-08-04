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

use Help\PrestaShop\DependencyInjection\ApplicationCompilerPass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class ApplicationCompilerPassTest extends TestCase
{
    private ApplicationCompilerPass $applicationCompilerPass;
    /** @var ContainerBuilder&MockObject */
    private $containerMock;
    private mixed $mapping16;
    private mixed $mapping17;
    private mixed $mapping8;

    public function setUp(): void
    {
        $this->applicationCompilerPass = new ApplicationCompilerPass(__DIR__ . '/../ressources/config');
        $this->containerMock = $this->createMock(ContainerBuilder::class);
        $this->mapping16 = Yaml::parse(file_get_contents(__DIR__ . '/../ressources/config/mapping_v1.6.yml') ?: '');
        $this->mapping17 = Yaml::parse(file_get_contents(__DIR__ . '/../ressources/config/mapping_v1.7.yml') ?: '');
        $this->mapping8 = Yaml::parse(file_get_contents(__DIR__ . '/../ressources/config/mapping_v8.0.yml') ?: '');
    }

    public function test16Mapping(): void
    {
        $_SERVER['REQUEST_URI'] = '/en/doc/AdminDashboard?version=1.6';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping16],
                ['language', 'en'],
                ['repository', 'user-documentation-1.6']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test16MappingFromAPI(): void
    {
        $_SERVER['REQUEST_URI'] = '/api?request=foo%3Dbar%26version=1.6&version=1.7';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping16],
                ['language', 'en'],
                ['repository', 'user-documentation-1.6']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test16MappingWithUnavailableLanguage(): void
    {
        $_SERVER['REQUEST_URI'] = '/it/doc/AdminDashboard?version=1.6';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping16],
                ['language', 'en'],
                ['repository', 'user-documentation-1.6']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test17Mapping(): void
    {
        $_SERVER['REQUEST_URI'] = '/en/doc/AdminDashboard?version=1.7';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping17],
                ['language', 'en'],
                ['repository', 'user-documentation-en']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test17MappingFromAPI(): void
    {
        $_SERVER['REQUEST_URI'] = '/api?request=foo%3Dbar%26version=1.7%26language=fr';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping17],
                ['language', 'fr'],
                ['repository', 'user-documentation-fr']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test17MappingWithFrenchLanguage(): void
    {
        $_SERVER['REQUEST_URI'] = '/fr/doc/AdminDashboard?version=1.7';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping17],
                ['language', 'fr'],
                ['repository', 'user-documentation-fr']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test17MappingWithFromOlderVersion(): void
    {
        $_SERVER['REQUEST_URI'] = '/en/doc/AdminDashboard?version=1.4';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping17],
                ['language', 'en'],
                ['repository', 'user-documentation-en']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test17MappingWithFromOlderVersionFromAPI(): void
    {
        $_SERVER['REQUEST_URI'] = '/api?request=foo%3Dbar%26version=1.4%26language=en';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping17],
                ['language', 'en'],
                ['repository', 'user-documentation-en']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test8Mapping(): void
    {
        $_SERVER['REQUEST_URI'] = '/en/doc/AdminDashboard?version=8.0';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping8],
                ['language', 'en'],
                ['repository', 'user-documentation-v8-en']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test8MappingFromAPI(): void
    {
        $_SERVER['REQUEST_URI'] = '/api?request=foo%3Dbar%26version=8.0%26language=fr';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping8],
                ['language', 'fr'],
                ['repository', 'user-documentation-v8-fr']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test8MappingFromUnexistingVersion(): void
    {
        $_SERVER['REQUEST_URI'] = '/en/doc/AdminDashboard?version=12.4';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping8],
                ['language', 'en'],
                ['repository', 'user-documentation-v8-en']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }

    public function test8MappingFromUnexistingVersionFromAPI(): void
    {
        $_SERVER['REQUEST_URI'] = '/api?request=foo%3Dbar%26version=12.5%26language=fr';

        $this->containerMock->expects($this->exactly(3))
            ->method('setParameter')
            ->withConsecutive(
                ['mapping', $this->mapping8],
                ['language', 'fr'],
                ['repository', 'user-documentation-v8-fr']
            )
        ;

        $this->applicationCompilerPass->process($this->containerMock);
    }
}
