<?php

$config = new PrestaShop\CodingStandards\CsFixer\Config();

$config
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/var/.php_cs.cache')
    ->getFinder()
    ->in(__DIR__)
    ->exclude('vendor');

return $config;