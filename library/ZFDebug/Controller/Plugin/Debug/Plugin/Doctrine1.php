<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @author     aur1mas <aur1mas@devnet.lt>
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Doctrine1 extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * plugin identified name
     *
     * @var string
     */
    protected $identifier = 'doctrine1';

    /**
     * @var \Doctrine_Connection_Profiler
     */
    protected $profiler = null;

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Dprofiler
     *
     * @param null|\Doctrine_Connection_Profiler $profiler
     *
     * @author Paulius Petronis <paulius@art21.lt>
     */
    public function __construct($profiler = null)
    {
        if (!$profiler) {
            $profiler = new Doctrine_Connection_Profiler();
            $conn = Doctrine_Manager::connection();
            $conn->setListener($profiler);
        }
        $this->profiler = $profiler;
    }

    /**
     * Gets menu tab for the Debug Bar
     *
     * @author Paulius Petronis <paulius@art21.lt>
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
     * @author Paulius Petronis <paulius@art21.lt>
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
     * returns a unique identifier for the specific plugin
     *
     * @author aur1mas <aur1mas@devnet.lt>
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
