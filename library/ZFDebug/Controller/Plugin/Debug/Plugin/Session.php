<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 * @version    $Id$
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Session extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'session';

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Variables
     */
    public function __construct()
    {
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Returns the base64 encoded icon
     *
     * @return string
     *
     */
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFWSURBVBgZBcE/SFQBAAfg792dppJeEhjZn80MChpqdQ2iscmlscGi1nBPaGkviKKhONSpvSGHcCrBiDDjEhOC0I68sjvf+/V9RQCsLHRu7k0yvtN8MTMPICJieaLVS5IkafVeTkZEFLGy0JndO6vWNGVafPJVh2p8q/lqZl60DpIkaWcpa1nLYtpJkqR1EPVLz+pX4rj47FDbD2NKJ1U+6jTeTRdL/YuNrkLdhhuAZVP6ukqbh7V0TzmtadSEDZXKhhMG7ekZl24jGDLgtwEd6+jbdWAAEY0gKsPO+KPy01+jGgqlUjTK4ZroK/UVKoeOgJ5CpRyq5e2qjhF1laAS8c+Ymk1ZrVXXt2+9+fJBYUwDpZ4RR7Wtf9u9m2tF8Hwi9zJ3/tg5pW2FHVv7eZJHd75TBPD0QuYze7n4Zdv+ch7cfg8UAcDjq7mfwTycew1AEQAAAMB/0x+5JQ3zQMYAAAAASUVORK5CYII=';
    }

    /**
     * Gets menu tab for the DebugBar
     *
     * @return string
     */
    public function getTab()
    {
        return ' Session';
    }

    /**
     * Gets content panel for the DebugBar
     *
     * @return string
     */
    public function getPanel()
    {
        $vars = '<div style="width:50%;float:left;">';
        $vars .= '<h4>Session</h4>';
        $vars .= '<div id="ZFDebug_session" style="margin-left:-22px">' . $this->_cleanData($_SESSION) . '</div>';
        $vars .= '</div><div style="clear:both">&nbsp;</div>';

        return $vars;
    }

    /**
     * Transforms data into readable format
     *
     * @param array $values
     *
     * @return string
     */
    protected function _cleanData($values)
    {
        $linebreak = $this->getLinebreak();

        if (is_array($values)) {
            ksort($values);
        }
        $retVal = '<div class="pre">';
        foreach ($values as $key => $value) {
            $key = htmlspecialchars($key);
            if (is_numeric($value)) {
                $retVal .= $key . ' => ' . $value . $linebreak;
            } else {
                if (is_string($value)) {
                    $retVal .= $key . ' => \'' . htmlspecialchars($value) . '\'' . $linebreak;
                } else {
                    if (is_array($value)) {
                        $retVal .= $key . ' => Array' . $linebreak;
                        $retVal .= self::_cleanData($value);
                    } else {
                        if (is_object($value)) {
                            $array = array();
                            foreach ($value as $member => $data) {
                                $array[$member] = $data;
                            }
                            $retVal .= $key . ' => ' . get_class($value) . ' Object' . $linebreak;
                            $retVal .= self::_cleanData($array);
                        } else {
                            if (is_null($value)) {
                                $retVal .= $key . ' => NULL' . $linebreak;
                            }
                        }
                    }
                }
            }
        }

        return $retVal . '</div>';
    }
}
