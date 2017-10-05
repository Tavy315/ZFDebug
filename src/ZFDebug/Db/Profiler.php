<?php
namespace ZFDebug\Db;

use ZFDebug\Db\Profiler\Query;

/**
 * Class Profiler.
 *
 * @package ZFDebug\Db
 * @author  Octavian Matei <octav@octav.name>
 * @since   04.10.2017
 */
class Profiler extends \Zend_Db_Profiler
{
    /**
     * Starts a query. Creates a new query profile object (Zend_Db_Profiler_Query)
     * and returns the "query profiler handle".  Run the query, then call
     * queryEnd() and pass it this handle to make the query as ended and
     * record the time.  If the profiler is not enabled, this takes no
     * action and immediately returns null.
     *
     * @param string $queryText SQL statement
     * @param int    $queryType OPTIONAL Type of query, one of the Zend_Db_Profiler::* constants
     *
     * @return int|null
     */
    public function queryStart($queryText, $queryType = null)
    {
        if (!$this->_enabled) {
            return null;
        }

        $trace = array_map(
            function ($t) {
                return (isset($t['class']) ? $t['class'] : '')
                    . (isset($t['type']) ? $t['type'] : '')
                    . $t['function'] . '() ' . $t['file'] . ':' . $t['line'];
            },
            array_values(
                array_filter(
                    debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                    function ($t) {
                        return (strstr($t['file'], 'application') !== false && strstr($t['class'], 'Zend_Controller_Front') === false)
                            || (strstr($t['file'], 'library') !== false && strstr($t['file'], 'vendor') === false);
                    }
                )
            )
        );

        // make sure we have a query type
        if ($queryType === null) {
            switch (strtolower(substr(ltrim($queryText), 0, 6))) {
                case 'insert':
                    $queryType = self::INSERT;
                    break;

                case 'update':
                    $queryType = self::UPDATE;
                    break;

                case 'delete':
                    $queryType = self::DELETE;
                    break;

                case 'select':
                    $queryType = self::SELECT;
                    break;

                default:
                    $queryType = self::QUERY;
                    break;
            }
        }

        $this->_queryProfiles[] = new Query($queryText, $queryType, $trace);

        end($this->_queryProfiles);

        return key($this->_queryProfiles);
    }
}
