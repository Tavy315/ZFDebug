<?php

class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{
    protected function _initZFDebug()
    {
        // Setup autoloader with namespace
        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('ZFDebug');

        // Ensure the front controller is initialized
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');

        // Only enable zfdebug if options have been specified for it
        if ($this->hasOption('zfdebug')) {
            // Create ZFDebug instance
            $zfDebug = new \ZFDebug\Controller\Plugin\Debug($this->getOption('zfdebug'));

            // Register ZFDebug with the front controller
            $front->registerPlugin($zfDebug);
        }
        // In application.ini do the following:
        // 
        // [development : production]
        // zfdebug.plugins.Variables = null
        // zfdebug.plugins.Time = null
        // zfdebug.plugins.Memory = null
        // ...

        // Plugins that take objects as parameters like Database and Cache
        // need to be registered manually:
        $zfDebug->registerPlugin(new \ZFDebug\Controller\Plugin\Debug\Plugin\Database($db));

        // Alternative configuration without application.ini
        $options = [
            'plugins' => [
                'variables',
                'database',
                'file' => [ 'basePath' => '/Library/WebServer/Documents/budget', 'myLibrary' => 'Scienta' ],
                'memory',
                'time',
                'registry',
                //'auth',
                //'cache' => array('backend' => $cache->getBackend()),
                'exception',
            ],
        ];
        $zfDebug = new \ZFDebug\Controller\Plugin\Debug($options);
        // Register ZFDebug with the front controller
        $front->registerPlugin($zfDebug);
    }
}
