<?php
/**
 * Defining bootstrap for Zend Framework pre-1.8
 */

// Leave 'Database' options empty to rely on Zend_Db_Table default adapter

$options = [
    // 'jquery_path' => 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js',
    'plugins' => [
        'Variables',
        'Constants',
        'Html',
        'Database' => [ 'adapter' => [ 'standard' => $db ] ],
        'File'     => [ 'basePath' => 'path/to/application/root' ],
        'Memory',
        'Time',
        'Registry',
        'Cache'    => [ 'backend' => $cache->getBackend() ],
        'Exception',
    ],
];

$debug = new \ZFDebug\Controller\Plugin\Debug($options);
$frontController->registerPlugin($debug);

// Alternative registration of plugins, also possible elsewhere in dispatch process
$zfDebug = \Zend_Controller_Front::getInstance()->getPlugin('Debug');
$zfDebug->registerPlugin(new \ZFDebug\Controller\Plugin\Debug\Plugin\Database($optionsArray));

/**
 * Registering other plugins and start dispatch
 */
