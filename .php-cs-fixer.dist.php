<?php
$config = new PhpCsFixer\Config();
$config->setUsingCache(false);
$config->setRiskyAllowed(true);


$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var', 'vendor', 'vendor85'])
;

$config->setFinder($finder);

return $config;
