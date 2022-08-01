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

namespace Help\PrestaShop\DependencyInjection;

use Help\PrestaShop\RequestInfo;
use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Yaml\Yaml;

class ApplicationCompilerPass implements CompilerPassInterface
{
    private const FALLBACK_VERSION = '1.7';
    private const FALLBACK_LANGUAGE = 'en';

    private string $configDir;

    public function __construct(string $configDir)
    {
        $this->configDir = rtrim($configDir, '/');
    }

    public function process(ContainerBuilder $container): void
    {
        $requestInfo = RequestInfo::fromRequestUri($_SERVER['REQUEST_URI']);

        $container->set(RequestInfo::class, $requestInfo);
        $container->setDefinition(RequestInfo::class, (new Definition(RequestInfo::class))->setSynthetic(true));

        $langRepositories = Yaml::parse(file_get_contents($this->getRepositoryMappingFilename()) ?: '');
        if (!is_array($langRepositories)) {
            throw new RuntimeException('Unable to get the repository mapping');
        }

        krsort($langRepositories);
        $latestVersion = (string) key($langRepositories);

        $version = $this->getVersion($requestInfo->getVersion());
        if (!file_exists($this->getMappingFilename($version))) {
            if (version_compare($version, $latestVersion, '>')) {
                $version = $latestVersion;
            } else {
                $version = self::FALLBACK_VERSION;
            }
        }

        $mapping = Yaml::parse(file_get_contents($this->getMappingFilename($version)) ?: '');
        if (!is_array($mapping)) {
            throw new RuntimeException('Unable to get the version mapping');
        }

        $language = $requestInfo->getLanguage();
        if (!isset($langRepositories[$version][$language])) {
            $language = self::FALLBACK_LANGUAGE;
        }

        $container->setParameter('mapping', $mapping);
        $container->setParameter('language', $language);
        $container->setParameter('repository', $langRepositories[$version][$language]);
    }

    private function getVersion(?string $version): string
    {
        if ($version === null) {
            return self::FALLBACK_VERSION;
        }

        $version = preg_replace('/[^0-9.]/', '', $version) ?? '';

        return str_pad(join('.', array_slice(explode('.', $version), 0, 2)), 3, '.0');
    }

    private function getMappingFilename(string $version): string
    {
        return sprintf($this->configDir . '/mapping_v%s.yml', $version);
    }

    private function getRepositoryMappingFilename(): string
    {
        return $this->configDir . '/mapping_repositories.yml';
    }
}
