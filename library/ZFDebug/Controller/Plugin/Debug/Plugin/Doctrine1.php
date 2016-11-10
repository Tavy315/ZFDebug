<?php
namespace ZFDebug\Controller\Plugin\Debug\Plugin;

use ZFDebug\Controller\Plugin\Debug\Plugin;

/**
 * Class Doctrine1
 *
 * @package ZFDebug\Controller\Plugin\Debug\Plugin
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Doctrine1 extends Plugin implements PluginInterface
{
    /**
     * plugin identified name
     *
     * @var string
     */
    protected $identifier = 'doctrine1';

    /** @var \Doctrine_Connection_Profiler */
    protected $profiler = null;

    /**
     * @param \Doctrine_Connection_Profiler|null $profiler
     */
    public function __construct($profiler = null)
    {
        if (!$profiler) {
            $profiler = new \Doctrine_Connection_Profiler();
            $conn = \Doctrine_Manager::connection();
            $conn->setListener($profiler);
        }
        $this->profiler = $profiler;
    }

    /**
     * Gets menu tab for the Debug Bar
     *
     * @return string
     */
    public function getTab()
    {
        if (!$this->profiler) {
            return 'No profiler';
        }

        $time = 0;
        foreach ($this->profiler as $event) {
            $time += $event->getElapsedSecs();
        }
        $html = 'Query: ' . $this->profiler->count() . ' in ' . round($time * 1000, 2) . ' ms';

        return $html;
    }

    /**
     * Gets content panel for the Debug Bar
     *
     * @return string
     */
    public function getPanel()
    {
        if (!$this->profiler) {
            return '';
        }

        $html = '<h4>Database queries</h4>';
        $html .= '<ol>';
        foreach ($this->profiler as $event) {
            $html .= '<li><strong>' . $event->getName() . ' ' . sprintf('%f', $event->getElapsedSecs()) . '</strong><br/>';
            $html .= $event->getQuery() . '<br />';
            $params = $event->getParams();
            if (is_array($params) && !empty($params)) {
                $html .= '<fieldset><legend>Params:</legend>';
                $html .= '<ol>';
                foreach ($params as $key => $value) {
                    $html .= '<li>' . $key . ' = ' . $value . '</li>';
                }
                $html .= '</ol>';
                $html .= '</fieldset>';
            }
        }
        $html .= '</ol>';

        return $html;
    }

    /**
     * Returns an unique identifier for the specific plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
