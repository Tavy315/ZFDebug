<?php
namespace ZFDebug\Controller\Plugin\Debug\Plugin;

use ZFDebug\Controller\Plugin\Debug\Plugin;
use ZFDebug\Db\Profiler;

/**
 * Class Database.
 *
 * @package ZFDebug\Controller\Plugin\Debug\Plugin
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Database extends Plugin implements PluginInterface
{
    /** @var array */
    protected $db = [];

    /** @var array */
    protected $executedQueries = [];

    /** @var bool */
    protected $explain = false;

    /** @var string */
    protected $identifier = 'database';

    /** @var string */
    protected $profiler = '';

    /** @var bool */
    protected $_backtrace = false;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!isset($options['adapter']) || !count($options['adapter'])) {
            if (\Zend_Db_Table_Abstract::getDefaultAdapter()) {
                $adapter = \Zend_Db_Table_Abstract::getDefaultAdapter();
                if (isset($options['backtrace']) && $options['backtrace']) {
                    $this->_backtrace = true;
                    $adapter->setProfiler(new Profiler(true));
                } else {
                    $adapter->getProfiler()->setEnabled(true);
                }
                $this->db[0] = $adapter;
            }
        } elseif ($options['adapter'] instanceof \Zend_Db_Adapter_Abstract) {
            $adapter = $options['adapter'];
            $adapter->getProfiler()->setEnabled(true);
            $this->db[0] = $adapter;
        } else {
            foreach ($options['adapter'] as $name => $adapter) {
                if ($adapter instanceof \Zend_Db_Adapter_Abstract) {
                    $adapter->getProfiler()->setEnabled(true);
                    $this->db[$name] = $adapter;
                }
            }
        }

        if (isset($options['explain'])) {
            $this->explain = (bool) $options['explain'];
        }
    }

    /**
     * Gets identifier for this plugin.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the base64 encoded icon.
     *
     * @return string
     **/
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEYSURBVBgZBcHPio5hGAfg6/2+R980k6wmJgsJ5U/ZOAqbSc2GnXOwUg7BESgLUeIQ1GSjLFnMwsKGGg1qxJRmPM97/1zXFAAAAEADdlfZzr26miup2svnelq7d2aYgt3rebl585wN6+K3I1/9fJe7O/uIePP2SypJkiRJ0vMhr55FLCA3zgIAOK9uQ4MS361ZOSX+OrTvkgINSjS/HIvhjxNNFGgQsbSmabohKDNoUGLohsls6BaiQIMSs2FYmnXdUsygQYmumy3Nhi6igwalDEOJEjPKP7CA2aFNK8Bkyy3fdNCg7r9/fW3jgpVJbDmy5+PB2IYp4MXFelQ7izPrhkPHB+P5/PjhD5gCgCenx+VR/dODEwD+A3T7nqbxwf1HAAAAAElFTkSuQmCC';
    }

    /**
     * Gets content panel for the Debug Bar.
     *
     * @return string
     */
    public function getPanel()
    {
        if (!$this->db) {
            return '';
        }

        $html = '<h4>Database queries';

        // This is probably always on
        if (\Zend_Db_Table_Abstract::getDefaultMetadataCache()) {
            $html .= ' – Metadata cache ENABLED';
        } else {
            $html .= ' – Metadata cache DISABLED';
        }

        $html .= '</h4>';

        return $html . $this->getProfile() . $this->repeatedQueries();
    }

    /**
     * @return string
     */
    public function getProfile()
    {
        if (!empty($this->profiler)) {
            return $this->profiler;
        }

        $html = '';

        /**
         * @var string                    $name
         * @var \Zend_Db_Adapter_Abstract $adapter
         */
        foreach ($this->db as $name => $adapter) {
            if ($profiles = $adapter->getProfiler()->getQueryProfiles()) {
                $adapter->getProfiler()->setEnabled(false);
                if (count($this->db) > 1) {
                    $html .= '<h4>Adapter ' . $name . '</h4>';
                }
                $html .= '<table cellspacing="0" cellpadding="0" width="100%">';

                /** @var \Zend_Db_Profiler_Query $profile */
                foreach ($profiles as $profile) {
                    $html .= '<tr>' . PHP_EOL . '<td style="text-align:right;padding-right:2em;width:10em;" nowrap>' . PHP_EOL
                        . sprintf('%0.2f', $profile->getElapsedSecs() * 1000) . 'ms</td>' . PHP_EOL . '<td>';

                    $params = $profile->getQueryParams();
                    array_walk($params, [ $this, 'addQuotes' ]);
                    $paramCount = count($params);
                    if ($paramCount) {
                        $executedQuery = htmlspecialchars(preg_replace(array_fill(0, $paramCount, '/\?/'), $params, $profile->getQuery(), 1));
                    } else {
                        $executedQuery = htmlspecialchars($profile->getQuery());
                    }

                    $this->executedQueries[$executedQuery][] = $executedQuery;
                    $html .= $executedQuery;

                    $supportedAdapter = ($adapter instanceof \Zend_Db_Adapter_Mysqli || $adapter instanceof \Zend_Db_Adapter_Pdo_Mysql);

                    // Run explain if enabled, supported adapter and SELECT query
                    if ($this->explain && $supportedAdapter) {
                        $html .= '</td><td style="color:#7F7F7F;padding-left:2em;" nowrap>';

                        foreach ($adapter->fetchAll('EXPLAIN ' . $profile->getQuery()) as $explain) {
                            $html .= '<div style="padding-bottom:0.5em">';
                            $explainData = [
                                'Type'          => $explain['select_type'] . ', ' . $explain['type'],
                                'Table'         => $explain['table'],
                                'Possible keys' => str_replace(',', ', ', $explain['possible_keys']),
                                'Key used'      => $explain['key'],
                            ];
                            if ($explain['Extra']) {
                                $explainData['Extra'] = $explain['Extra'];
                            }
                            $explainData['Rows'] = $explain['rows'];

                            foreach ($explainData as $key => $value) {
                                $html .= $key . ': <span style="color:#ffb13e">' . $value . '</span><br>' . PHP_EOL;
                            }
                            $html .= '</div>';
                        }
                    }

                    $html .= '</td>' . PHP_EOL . '</tr>' . PHP_EOL;

                    if ($this->_backtrace) {
                        $trace = $profile->getTrace();

                        if (count($trace) > 0) {
                            array_walk(
                                $trace,
                                function (&$v, $k) {
                                    $v = ($k + 1) . '. ' . $v;
                                }
                            );

                            $html .= '<tr>' . PHP_EOL
                                . '<td></td>' . PHP_EOL
                                . '<td>' . implode('<br />', $trace) . '</td>'
                                . PHP_EOL . '</tr>' . PHP_EOL;
                        }
                    }
                }
                $html .= '</table>' . PHP_EOL;
            }
        }

        $this->profiler = $html;

        return $html;
    }

    /**
     * Gets menu tab for the Debug Bar.
     *
     * @return string
     */
    public function getTab()
    {
        if (!$this->db) {
            return 'No adapter';
        }

        $adapterInfo = [];

        /** @var \Zend_Db_Adapter_Abstract $adapter */
        foreach ($this->db as $adapter) {
            $profiler = $adapter->getProfiler();
            $adapterInfo[] = $profiler->getTotalNumQueries() . ' in '
                . round($profiler->getTotalElapsedSecs() * 1000, 2) . ' ms';
        }
        $html = implode(' / ', $adapterInfo);

        return $html;
    }

    // For adding quotes to query params
    protected function addQuotes(&$value, $key)
    {
        $value = "'" . $value . "'";
    }

    /**
     * @return string
     */
    protected function repeatedQueries()
    {
        if (!empty($this->executedQueries)) {
            $this->executedQueries = array_filter(
                $this->executedQueries,
                function ($value) {
                    return count($value) > 1;
                }
            );

            if (!empty($this->executedQueries)) {
                $queries = '<h4>Repeated Queries</h4>'
                    . '<table cellspacing="0" cellpadding="0" width="100%">';

                foreach ($this->executedQueries as $query => $regs) {
                    $queries .= '<tr>' . PHP_EOL . '<td style="text-align:right;padding-right:2em;width:10em;" nowrap>' . PHP_EOL
                        . count($regs) . '</td>' . PHP_EOL . '<td>' . $query . '</td>' . PHP_EOL . '</tr>';
                }

                $queries .= '</table>' . PHP_EOL;

                return $queries;
            }
        }
    }
}
