<?php
namespace ZFDebug\Db\Profiler;

/**
 * Class Query.
 *
 * @package ZFDebug\Db\Profiler
 * @author  Octavian Matei <octav@octav.name>
 * @since   04.10.2017
 */
class Query extends \Zend_Db_Profiler_Query
{
    /** @var array */
    protected $_trace = [];

    /**
     * Class constructor.  A query is about to be started, save the query text ($query) and its
     * type (one of the Zend_Db_Profiler::* constants).
     *
     * @param string $query
     * @param int    $queryType
     * @param array  $trace
     *
     * @return void
     */
    public function __construct($query, $queryType, $trace)
    {
        $this->_trace = $trace;

        parent::__construct($query, $queryType);
    }

    /**
     * @return array
     */
    public function getTrace()
    {
        return $this->_trace;
    }
}
