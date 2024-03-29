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

use Help\PrestaShop\ProviderInfo;
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
    private const FALLBACK_CONTROLLER = 'GettingStarted';

    private string $configDir;

    public function __construct(string $configDir)
    {
        $this->configDir = rtrim($configDir, '/');
    }

    public function process(ContainerBuilder $container): void
    {
        $requestInfo = RequestInfo::fromRequestUri($_SERVER['REQUEST_URI']);

        $langUrls = Yaml::parse(file_get_contents($this->getLangUrlsMappingFilename()) ?: '');
        if (!is_array($langUrls)) {
            throw new RuntimeException('Unable to get the repository mapping');
        }

        krsort($langUrls);
        $latestVersion = (string) key($langUrls);

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
        if ($language === null || !isset($langUrls[$version]['github'][$language])) {
            $language = self::FALLBACK_LANGUAGE;
        }

        $controller = $requestInfo->getController();
        if ($controller === null || !isset($mapping[$controller])) {
            $controller = self::FALLBACK_CONTROLLER;
        }

        $providerInfo = new ProviderInfo($requestInfo, $version, $language, $controller);
        $container->set(ProviderInfo::class, $providerInfo);
        $container->setDefinition(ProviderInfo::class, (new Definition(ProviderInfo::class))->setSynthetic(true));

        $baseSlug = implode('/', array_slice(explode('/', $mapping[$controller][$language]), 0, -1));
        $relativeLinkUrl = !isset($langUrls[$version]['gitbook'][$language]) || !is_string($container->getParameter('docs_base_url'))
            ? null
            : $container->getParameter('docs_base_url') . '/' . $langUrls[$version]['gitbook'][$language] . '/' . $baseSlug
        ;

        $container->setParameter('mapping', $mapping);
        $container->setParameter('repository', $langUrls[$version]['github'][$language]);
        $container->setParameter('markdown_config', [
            'gitbook' => [
                'relative_link_url' => $relativeLinkUrl,
                'relative_img_url' => 'https://raw.githubusercontent.com/PrestaShop/' . $langUrls[$version]['github'][$language] . '/master',
            ],
        ]);
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

    private function getLangUrlsMappingFilename(): string
    {
        return $this->configDir . '/mapping_urls.yml';
    }
}
