<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../../vendor/autoload.php');
}

const LOG_FILE = 'php://stdout';
const STREAM_URL_PATTERN = 'http://doc.prestashop.com/rest/api/content/PAGE_ID?expand=body.view';
const STREAM_OPTIONS = [
    'http' => [
        'header' => ['Host:doc.prestashop.com'],
    ],
];
const TEMPLATES_PATH = __DIR__ . '/../../views';
const MAPPING_FILES_PATH = __DIR__ . '/../../config/mappings';
