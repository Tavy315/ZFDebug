<?php
namespace ZFDebug\Controller\Plugin\Debug\Plugin;

use ZFDebug\Controller\Plugin\Debug\Plugin;

/**
 * Class Html
 *
 * @package ZFDebug\Controller\Plugin\Debug\Plugin
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Html extends Plugin implements PluginInterface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $identifier = 'html';

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
     **/
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEdSURBVDjLjZIxTgNBDEXfbDZIlIgmCKWgSpMGxEk4AHehgavQcJY0KRKJJiBQLkCR7PxvmiTsbrJoLY1sy/Ibe+an9XodtqkfSUd+Op0mTlgpidFodKpGRAAwn8/pstI2AHvfbi6KAkndgHZx31iP2/CTE3Q1A0ji6fUjsiFn8fJ4k44mSCmR0sl3QhJXF2fYwftXPl5hsVg0Xr0d2yZnIwWbqrlyOZlMDtc+v33H9eUQO7ACOZAC2Ye8qqIJqCfZRtnIIBnVQH8AdQOqylTZWPBwX+zGj93ZrXU7ZLlcxj5vArYi5/Iweh+BNQCbrVl8/uAMvjvvJbBU/++6rVarGI/HB0BbI4PBgNlsRtGlsL4CK7sAfQX2L6CPwH4BZf1E9tbX5ioAAAAASUVORK5CYII=';
    }

    /**
     * Gets menu tab for the Debug Bar
     *
     * @return string
     */
    public function getTab()
    {
        return 'HTML';
    }

    /**
     * Gets content panel for the Debug Bar
     *
     * @return string
     */
    public function getPanel()
    {
        $body = \Zend_Controller_Front::getInstance()->getResponse()->getBody();

        if ('' == $body) {
            return '';
        }

        $errors = libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($body);

        libxml_use_internal_errors($errors);

        $linebreak = $this->getLinebreak();

        return '<h4>HTML Information</h4>'
        . $this->isXhtml()
        . $dom->getElementsByTagName('*')->length . ' Tags in ' . round(strlen($body) / 1024, 2) . 'K' . $linebreak
        . $dom->getElementsByTagName('link')->length . ' Link Tags' . $linebreak
        . $dom->getElementsByTagName('script')->length . ' Script Tags' . $linebreak
        . $dom->getElementsByTagName('img')->length . ' Images' . $linebreak
        . '<form method="post" action="http://validator.w3.org/check"><p>'
        . '<input type="hidden" name="fragment" value="' . htmlentities($body) . '"' . $this->getClosingBracket()
        . '<input type="submit" value="Validate With W3C"' . $this->getClosingBracket()
        . '</p></form>';
    }
}
