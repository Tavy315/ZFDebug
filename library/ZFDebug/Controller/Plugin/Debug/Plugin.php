<?php
namespace ZFDebug\Controller\Plugin\Debug;

/**
 * Class Plugin
 *
 * @package ZFDebug\Controller\Plugin\Debug
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Plugin
{
    /** @var null|string */
    protected $closingBracket = null;

    public function getLinebreak()
    {
        return '<br' . $this->getClosingBracket();
    }

    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHhSURBVDjLpZI9SJVxFMZ/r2YFflw/kcQsiJt5b1ije0tDtbQ3GtFQYwVNFbQ1ujRFa1MUJKQ4VhYqd7K4gopK3UIly+57nnMaXjHjqotnOfDnnOd/nt85SURwkDi02+ODqbsldxUlD0mvHw09ubSXQF1t8512nGJ/Uz/5lnxi0tB+E9QI3D//+EfVqhtppGxUNzCzmf0Ekojg4fS9cBeSoyzHQNuZxNyYXp5ZM5Mk1ZkZT688b6thIBenG/N4OB5B4InciYBCVyGnEBHO+/LH3SFKQuF4OEs/51ndXMXC8Ajqknrcg1O5PGa2h4CJUqVES0OO7sYevv2qoFBmJ/4gF4boaOrg6rPLYWaYiVfDo0my8w5uj12PQleB0vcp5I6HsHAUoqUhR29zH+5B4IxNTvDmxljy3x2YCYUwZVlbzXJh9UKeQY6t2m0Lt94Oh5loPdqK3EkjzZi4MM/Y9Db3MTv/mYWVxaqkw9IOATNR7B5ABHPrZQrtg9sb8XDKa1+QOwsri4zeHD9SAzE1wxBTXz9xtvMc5ZU5lirLSKIz18nJnhOZjb22YKkhd4odg5icpcoyL669TAAujlyIvmPHSWXY1ti1AmZ8mJ3ElP1ips1/YM3H300g+W+51nc95YPEX8fEbdA2ReVYAAAAAElFTkSuQmCC';
    }

    public function getClosingBracket()
    {
        if (!$this->closingBracket) {
            if ($this->isXhtml()) {
                $this->closingBracket = ' />';
            } else {
                $this->closingBracket = '>';
            }
        }

        return $this->closingBracket;
    }

    protected function isXhtml()
    {
        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;

        /** @see \Zend_View_Helper_Doctype::doctype() */
        /** @var \Zend_View_Helper_Doctype */
        $docType = $view->doctype();

        /** @see \Zend_View_Helper_Doctype::isXhtml() */
        return $docType->isXhtml();
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
                $retVal .= '<a href="#" style="text-decoration:none" class="arrayexpandcollapse">&plusmn;&nbsp;' . $key . '</a> => ' . self::cleanData($value) . '<br />';
            } elseif (is_object($value)) {
                $retVal .= $key . ' => ' . get_class($value) . ' Object()' . $linebreak;
            } elseif (is_null($value)) {
                $retVal .= $key . ' => NULL' . $linebreak;
            }
        }

        return $retVal . '</div>';
    }
}
