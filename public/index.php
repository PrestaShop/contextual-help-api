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
use Help\PrestaShop\DocContentProvider;
use Help\PrestaShop\Http\GuzzleAdapter;
use Help\PrestaShop\Http\HttpClient;
use Help\PrestaShop\PageInfosBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//const LOG_FILE = __DIR__ . '/../var/logs/dev.log';
const LOG_FILE = 'php://stdout';
const STREAM_URL_PATTERN = 'http://doc.prestashop.com/rest/api/content/PAGE_ID?expand=body.view';
const STREAM_OPTIONS = [
    'http' => [
        'header' => ['Host:doc.prestashop.com'],
    ],
];
const TEMPLATES_PATH = __DIR__ . '/../views';
const MAPPING_FILES_PATH = __DIR__ . '/../config/mappings';

$templatesLoader = new FilesystemLoader(TEMPLATES_PATH);
$twig = new Environment($templatesLoader);

$logger = new Logger('logs');
$logger->pushHandler(new StreamHandler(LOG_FILE, Logger::WARNING));

$pageInfosBuilder = new PageInfosBuilder(MAPPING_FILES_PATH, $logger);

$httpAdapter = new GuzzleAdapter();
$httpClient = new HttpClient($httpAdapter);

$docContentProvider = new DocContentProvider($httpClient, STREAM_URL_PATTERN, STREAM_OPTIONS);

$contentBuilder = new ContentBuilder($docContentProvider, $pageInfosBuilder, $twig, $logger);

echo $contentBuilder->getContent($_SERVER['REQUEST_URI'], $_GET['version'] ?? null);
