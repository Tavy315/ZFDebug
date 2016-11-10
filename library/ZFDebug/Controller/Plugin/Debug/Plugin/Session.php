<?php
namespace ZFDebug\Controller\Plugin\Debug\Plugin;

use ZFDebug\Controller\Plugin\Debug\Plugin;

/**
 * Class Session
 *
 * @package ZFDebug\Controller\Plugin\Debug\Plugin
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Session extends Plugin implements PluginInterface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $identifier = 'session';

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
     * Returns the base64 encoded icon
     *
     * @return string
     */
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFWSURBVBgZBcE/SFQBAAfg792dppJeEhjZn80MChpqdQ2iscmlscGi1nBPaGkviKKhONSpvSGHcCrBiDDjEhOC0I68sjvf+/V9RQCsLHRu7k0yvtN8MTMPICJieaLVS5IkafVeTkZEFLGy0JndO6vWNGVafPJVh2p8q/lqZl60DpIkaWcpa1nLYtpJkqR1EPVLz+pX4rj47FDbD2NKJ1U+6jTeTRdL/YuNrkLdhhuAZVP6ukqbh7V0TzmtadSEDZXKhhMG7ekZl24jGDLgtwEd6+jbdWAAEY0gKsPO+KPy01+jGgqlUjTK4ZroK/UVKoeOgJ5CpRyq5e2qjhF1laAS8c+Ymk1ZrVXXt2+9+fJBYUwDpZ4RR7Wtf9u9m2tF8Hwi9zJ3/tg5pW2FHVv7eZJHd75TBPD0QuYze7n4Zdv+ch7cfg8UAcDjq7mfwTycew1AEQAAAMB/0x+5JQ3zQMYAAAAASUVORK5CYII=';
    }

    /**
     * Gets menu tab for the Debug Bar
     *
     * @return string
     */
    public function getTab()
    {
        return ' Session';
    }

    /**
     * Gets content panel for the Debug Bar
     *
     * @return string
     */
    public function getPanel()
    {
        $vars = '<div style="width:50%;float:left;">'
            . '<h4>Session</h4>'
            . '<div id="ZFDebug_session" style="margin-left:-22px">' . $this->cleanData($_SESSION) . '</div>'
            . '</div><div style="clear:both">&nbsp;</div>';

        return $vars;
    }

    /**
     * Transforms data into readable format
     *
     * @param array $values
     *
     * @return string
     */
    protected function cleanData($values)
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
            } elseif (is_string($value)) {
                $retVal .= $key . ' => \'' . htmlspecialchars($value) . '\'' . $linebreak;
            } elseif (is_array($value)) {
                $retVal .= $key . ' => Array' . $linebreak;
                $retVal .= self::cleanData($value);
            } elseif (is_object($value)) {
                $array = [];
                foreach ($value as $member => $data) {
                    $array[$member] = $data;
                }
                $retVal .= $key . ' => ' . get_class($value) . ' Object' . $linebreak;
                $retVal .= self::cleanData($array);
            } elseif (is_null($value)) {
                $retVal .= $key . ' => NULL' . $linebreak;
            }
        }

        return $retVal . '</div>';
    }
}
