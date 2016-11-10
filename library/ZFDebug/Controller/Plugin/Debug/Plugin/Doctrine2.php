<?php
namespace ZFDebug\Controller\Plugin\Debug\Plugin;

use ZFDebug\Controller\Plugin\Debug\Plugin;

/**
 * Class Doctrine2
 *
 * @package ZFDebug\Controller\Plugin\Debug\Plugin
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Doctrine2 extends Plugin implements PluginInterface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $identifier = 'doctrine2';

    /**
     * Contains entityManagers
     *
     * @var array
     */
    protected $em = [];

    /**
     * If true, try to use sqlparse to prettify queries
     * requires sqlparse to be installed on the server.
     *
     * @see http://code.google.com/p/python-sqlparse/
     * @var bool
     */
    public static $sqlParseEnabled = false;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['entityManagers'])) {
            $this->em = $options['entityManagers'];
        }
    }

    /**
     * Gets icon
     *
     * @return string
     */
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAidJREFUeNqUk9tLFHEUxz+/mdnZnV1Tt5ttWVC+pBG+9RAYRNBDICT5D1hgL/VQWRAVEfVoCGURhCBFEj6IRkRFF7BAxPZlIbvZBTQq0q677u5c9tdvZyPaS1QHZh7OnPM93/me8xWC4rAnR6WbuAdSYjRvwWzaVFpSFEZpwvvwGnu4GwJB5OwMfwutNKHXrQFrASJcjTM+RPJMh/wvALOpRVh7+pC6gahegjMxQvLsTvnPAHkN5NxbhB5AfptDy4OMD5PsrQwiRElz5uoJvKdjaMsb0FesxX3yEBGsQiY/YWxopWpvv/gjg8zgSXJvEojapVid5wl3DRLc3qWYfCz8ztgQqf6DsiJA5vZFmZuKIyI1kPyC9zJOvjLYuh9zx2Hk5/doNXU4Dwawpx7JMgA3cVe9VT4YRl/djHOnDzd+vQDSdgiz7QAy9RUcG29ytPwOcrPTiEX1RI7fQqhJeDbSdRVmTn30CLUfhfnvZEdOI7PpChoYAVWo5rmOz0R6XoER4ueTx/IKsv8m/S8G+sp1OK8ukzq1DS1cS85OY+3qwWhs8W8ic+UIzv1LSqMoWjRWziCwsV1dkQWKnjf9WIm3z2/OR1Y12zcvqHWG0RbG0GIN5QDm+s3C3LrbXxmBECK6rLCdgWN+M5a6hew8oc7eIoOJUqulr/VI+8Y5pJP2p+VmnkEogrZ4FaGO7jJ3ikpezV+k93wC790L31R6faNPu5K1fwgwAMKf1kgHZKePAAAAAElFTkSuQmCC';
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Gets menu tab for the Debug Bar
     *
     * @return string
     */
    public function getTab()
    {
        if (!is_array($this->em) || !count($this->em)) {
            return 'No entity managers available';
        } else {
            foreach ($this->em as $em) {
                if (!$em instanceof \Doctrine\ORM\EntityManagerInterface) {
                    return "The entity manager you passed is not an instance of \\Doctrine\\ORM\\EntityManager";
                }
            }
        }

        $adapterInfo = [];

        foreach ($this->em as $em) {
            if ($logger = $em->getConnection()->getConfiguration()->getSqlLogger()) {
                $totalTime = 0;
                foreach ($logger->queries as $query) {
                    $totalTime += $query['executionMS'];
                }
                $adapterInfo[] = count($logger->queries) . ' in ' . round($totalTime * 1000, 2) . ' ms';
            }
        }
        $html = implode(' / ', $adapterInfo);

        if (!$html) {
            return 'Doctrine2 logger not enabled!';
        }

        return $html;
    }

    /**
     * Gets content panel for the Debug Bar
     *
     * @return string
     */
    public function getPanel()
    {
        if (!$this->em) {
            return '';
        }

        $html = '<h4>Doctrine2 queries - Doctrine2 (Common v' . \Doctrine\Common\Version::VERSION
            . ' | DBAL v' . \Doctrine\DBAL\Version::VERSION
            . ' | ORM v' . \Doctrine\ORM\Version::VERSION . ')</h4>';

        foreach ($this->em as $name => $em) {
            $html .= '<h4>EntityManager ' . $name . '</h4>';
            if ($logger = $em->getConnection()->getConfiguration()->getSqlLogger()) {
                $html .= $this->getProfile($logger);
            } else {
                $html .= 'No logger enabled!';
            }
        }

        return $html;
    }

    /**
     * Gets sql queries from the logger
     *
     * @param $logger
     *
     * @return string
     */
    protected function getProfile($logger)
    {
        $queries = '<table cellspacing="0" cellpadding="0" width="100%">';
        foreach ($logger->queries as $query) {
            $queries .= '<tr>' . PHP_EOL . '<td style="text-align:right;padding-right:2em;" nowrap>' . PHP_EOL . sprintf('%0.2f', round($query['executionMS'] * 1000, 2)) . 'ms</td>' . PHP_EOL . '<td>';

            $params = [];
            if (!empty($query['params'])) {
                $params = $query['params'];
                array_walk($params, [ $this, 'addQuotes' ]);
            }
            $paramCount = count($params);

            if ($paramCount) {
                $qry = htmlspecialchars(preg_replace(array_fill(0, $paramCount, '/\?/'), $params, $query['sql'], 1));
            } else {
                $qry = htmlspecialchars($query['sql']);
            }

            if (self::$sqlParseEnabled) {
                $qry = self::prettifySql($qry);
            }

            $queries .= $qry . '</td>' . PHP_EOL . '</tr>' . PHP_EOL;
        }
        $queries .= '</table>' . PHP_EOL;

        return $queries;
    }

    public static function prettifySql($qry)
    {
        $cmd = 'echo ' . escapeshellarg((string) $qry) . ' | sqlformat - --keywords=upper -r';
        exec($cmd, $output, $error);
        if (!$error) {
            $qry = '<pre>' . implode(PHP_EOL, $output) . '</pre>';
        }

        return $qry;
    }

    /**
     * Add quotes to query params
     *
     * @param mixed $value
     * @param mixed $key
     */
    protected function addQuotes(&$value, $key)
    {
        if (is_scalar($value)) {
            $value = "'" . $value . "'";
        } elseif ($value instanceof \DateTime) {
            // Try to accommodate for Doctrine's use of more advanced data types
            $value = "'" . $value->format('c') . "'";
        } elseif (is_array($value)) {
            $value = "'" . implode("', '", $value) . "'";
        } else {
            $value = "Object of type '" . get_class($value) . "'";
        }
    }
}
