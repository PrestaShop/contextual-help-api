<?php
declare(strict_types = 1);

define('_ROOT_DIR_', __DIR__. '/../../../');

// Add module composer autoloader
require_once _ROOT_DIR_ . 'vendor/autoload.php';

// Define constants to avoid errors during checks
const LOG_FILE = 'php://stdout';
const STREAM_URL_PATTERN = 'http://doc.prestashop.com/rest/api/content/PAGE_ID?expand=body.view';
const STREAM_OPTIONS = [
    'http' => [
        'header' => ['Host:doc.prestashop.com'],
    ],
];
const TEMPLATES_PATH = _ROOT_DIR_ . 'views';
const MAPPING_FILES_PATH = _ROOT_DIR_ . 'config/mappings';

// Make sure loader php-parser is coming from php stan composer

// 1- Use module vendors
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['vendor/nikic/php-parser/lib/PhpParser']);
$loader->register(true);
// 2- Use with Docker container
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['/composer/vendor/nikic/php-parser/lib/PhpParser']);
$loader->register(true);
// 3- Use with PHPStan phar
$loader = new \Composer\Autoload\ClassLoader();
// Contains the vendor in phar, like "phar://phpstan.phar/vendor"
$loader->setPsr4('PhpParser\\', ['phar://' . dirname($_SERVER['PATH_TRANSLATED']) . '/../phpstan/phpstan-shim/phpstan.phar/vendor/nikic/php-parser/lib/PhpParser/']);
$loader->register(true);
// 4- Use phpstan phar with sym link
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['phar://' . realpath($_SERVER['PATH_TRANSLATED']) . '/vendor/nikic/php-parser/lib/PhpParser/']);
$loader->register(true);
