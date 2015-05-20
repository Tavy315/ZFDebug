<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License New BSD License
 * @version    $Id$
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Locale extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'locale';

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Callback function to clear the cache
     *
     * @var string
     */
    protected $_callback;

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Variables
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        if (isset($options['callback']) && is_callable($options['callback'])) {
            $this->_callback = $options['callback'];
            call_user_func($this->_callback);
        }
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
     **/
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
        return ' Locale';
    }

    /**
     * Gets content panel for the DebugBar
     *
     * @return string
     */
    public function getPanel()
    {
        $linebreak = $this->getLinebreak();
        $html = '<h4> View Locale info</h4>' . $linebreak;
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $html .= 'Zend Locale is set to <strong>' . Zend_Registry::get('Zend_Locale') . '</strong>' . $linebreak;
            $html .= '<h4>Change Locale</h4>' . $linebreak;
            $html .= '<form method="post"><input name="debug_locale" type="text" placeholder="Locale..." class="input-small"></input></form>';
        } else {
            $html .= 'No Zend_Locale available.';
        }

        return $html;
    }
}
