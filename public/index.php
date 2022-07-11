<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Help\PrestaShop\ContentBuilder;
use Help\PrestaShop\ContentBuilderBodyProvider;
use Help\PrestaShop\ContentProviderBuilder;
use Help\PrestaShop\DocContentProvider;
use Help\PrestaShop\Http\GuzzleAdapter;
use Help\PrestaShop\Http\HttpClient;
use Help\PrestaShop\PageInfosBuilder;
use Help\PrestaShop\ProxyContentProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

// const LOG_FILE = __DIR__ . '/../var/logs/dev.log';
const LOG_FILE = 'php://stdout';
const STREAM_URL_PATTERN = 'http://doc.prestashop.com/rest/api/content/PAGE_ID?expand=body.view';
const PROXY_STREAM_URL_PATTERN = 'http://doc.prestashop.com/rest/REQUEST';
const STREAM_OPTIONS = [
    'http' => [
        'header' => ['Host:doc.prestashop.com'],
    ],
];
const TEMPLATES_PATH = __DIR__ . '/../views';
const MAPPING_FILES_PATH = __DIR__ . '/../config/mappings';
const TRANSLATIONS_FILE = __DIR__ . '/../config/translations.php';

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

$templatesLoader = new FilesystemLoader(TEMPLATES_PATH);
$twig = new Environment($templatesLoader);
$twig->addGlobal('ga_account_key', $_ENV['GA_ACCOUNT_KEY']);

$logger = new Logger('logs');
$logger->pushHandler(new StreamHandler(LOG_FILE, Logger::WARNING));

$pageInfosBuilder = new PageInfosBuilder(MAPPING_FILES_PATH, $logger);

$httpAdapter = new GuzzleAdapter();
$httpClient = new HttpClient($httpAdapter);

$docContentProvider = new DocContentProvider($httpClient, STREAM_URL_PATTERN, STREAM_OPTIONS);
$proxyContentProvider = new ProxyContentProvider($httpClient, PROXY_STREAM_URL_PATTERN, STREAM_OPTIONS);
$contentProviderBuilder = new ContentProviderBuilder($proxyContentProvider, $docContentProvider);
$contentBuilderBodyProvider = new ContentBuilderBodyProvider($twig, TRANSLATIONS_FILE);

$contentBuilder = new ContentBuilder($contentProviderBuilder, $contentBuilderBodyProvider, $pageInfosBuilder, $twig, $logger);

echo $contentBuilder->getContent($_SERVER['REQUEST_URI'], $_GET['version'] ?? null);
