<?php
namespace ZFDebug\Controller\Plugin\Debug\Plugin;

/**
 * Class Text
 *
 * @author  Octavian Matei <octav@octav.name>
 * @since   10.11.2016
 */
class Text implements PluginInterface
{
    /**
     * @var string
     */
    protected $tab = '';

    /**
     * @var string
     */
    protected $panel = '';

    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $identifier = 'text';

    /**
     * Contains plugin icon data
     *
     * @var string
     */
    protected $iconData = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHhSURBVDjLpZI9SJVxFMZ/r2YFflw/kcQsiJt5b1ije0tDtbQ3GtFQYwVNFbQ1ujRFa1MUJKQ4VhYqd7K4gopK3UIly+57nnMaXjHjqotnOfDnnOd/nt85SURwkDi02+ODqbsldxUlD0mvHw09ubSXQF1t8512nGJ/Uz/5lnxi0tB+E9QI3D//+EfVqhtppGxUNzCzmf0Ekojg4fS9cBeSoyzHQNuZxNyYXp5ZM5Mk1ZkZT688b6thIBenG/N4OB5B4InciYBCVyGnEBHO+/LH3SFKQuF4OEs/51ndXMXC8Ajqknrcg1O5PGa2h4CJUqVES0OO7sYevv2qoFBmJ/4gF4boaOrg6rPLYWaYiVfDo0my8w5uj12PQleB0vcp5I6HsHAUoqUhR29zH+5B4IxNTvDmxljy3x2YCYUwZVlbzXJh9UKeQY6t2m0Lt94Oh5loPdqK3EkjzZi4MM/Y9Db3MTv/mYWVxaqkw9IOATNR7B5ABHPrZQrtg9sb8XDKa1+QOwsri4zeHD9SAzE1wxBTXz9xtvMc5ZU5lirLSKIz18nJnhOZjb22YKkhd4odg5icpcoyL669TAAujlyIvmPHSWXY1ti1AmZ8mJ3ElP1ips1/YM3H300g+W+51nc95YPEX8fEbdA2ReVYAAAAAElFTkSuQmCC';

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['tab'])) {
            $this->setTab($options['tab']);
        }
        if (isset($options['panel'])) {
            $this->setPanel($options['panel']);
        }
    }

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
     * Sets identifier for this plugin
     *
     * @param string $name
     *
     * @return self Provides a fluent interface
     */
    public function setIdentifier($name)
    {
        $this->identifier = $name;

        return $this;
    }

    /**
     * Returns the base64 encoded icon
     *
     * @return string
     *
     */
    public function getIconData()
    {
        return $this->iconData;
    }

    /**
     * Sets icon data for this plugin
     *
     * @param string $data
     *
     * @return self Provides a fluent interface
     */
    public function setIconData($data)
    {
        $this->iconData = $data;

        return $this;
    }

    /**
     * Gets menu tab for the Debug Bar
     *
     * @return string
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Gets content panel for the Debug Bar
     *
     * @return string
     */
    public function getPanel()
    {
        return $this->panel;
    }

    /**
     * Sets tab content
     *
     * @param string $tab
     *
     * @return self Provides a fluent interface
     */
    public function setTab($tab)
    {
        $this->tab = $tab;

        return $this;
    }

    /**
     * Sets panel content
     *
     * @param string $panel
     *
     * @return self Provides a fluent interface
     */
    public function setPanel($panel)
    {
        $this->panel = $panel;

        return $this;
    }
}
