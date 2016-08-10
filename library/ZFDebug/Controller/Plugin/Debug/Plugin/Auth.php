<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License New BSD License
 * @version    $Id: $
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Auth extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $identifier = 'auth';

    /**
     * Contains Zend_Auth object
     *
     * @var Zend_Auth
     */
    protected $auth;

    /**
     * Contains "column name" for the username
     *
     * @var string
     */
    protected $user = 'user';

    /**
     * Contains "column name" for the role
     *
     * @var string
     */
    protected $role = 'role';

    /**
     * Function to get real world value for the user name
     *
     * @var string
     */
    protected $callback = null;

    /**
     * Contains ACL rules for this application
     *
     * @var Zend_Acl
     */
    protected $acl;

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Auth
     *
     * @var array
     */
    public function __construct(array $options = [])
    {
        $this->auth = Zend_Auth::getInstance();
        if (isset($options['user'])) {
            $this->user = $options['user'];
        }

        if (isset($options['role'])) {
            $this->role = $options['role'];
        }

        if (isset($options['callback']) && is_callable($options['callback'])) {
            $this->callback = $options['callback'];
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
     * Returns the base64 encoded icon
     *
     * @return string
     *
     */
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJ3SURBVDjLpZNtSNNRFIcNKunF1rZWBMJqKaSiX9RP1dClsjldA42slW0q5oxZiuHrlqllLayoaJa2jbm1Lc3QUZpKFmmaTMsaRp+kMgjBheSmTL2//kqMBJlFHx44XM7vOfdyuH4A/P6HFQ9zo7cpa/mM6RvCrVDzaVDy6C5JJKv6rwSnIhlFd0R0Up/GwF2KWyl01CTSkM/dQoQRzAurCjRCGnRUUE2FaoSL0HExiYVzsQwcj6RNrSqo4W5Gh6Yc4+1qDDTkIy+GhYK4nTgdz0H2PrrHUJzs71NQn86enPn+CVN9GnzruoYR63mMPbkC59gQzDl7pt7rc9f7FNyUhPY6Bx9gwt4E9zszhWWpdg6ZcS8j3O7zCTuEpnXB+3MNZkUUZu0NmHE8XsL91oSWwiiEc3MeseLrN6woYCWa/Zl8ozyQ3w3Hl2lYy0SwlCUvsVi/Gv2JwITnYPDun2Hy6jYuEzAF1jUBCVYpO6kXo+NuGMeBAgcgfwNkvgBOPgUqXgKvP7rBFvRhE1crp8Vq1noFYSlacVyqGk0D86gbART9BDk9BFnPCNJbCY5aCFL1Cyhtp0RWAp74MsKSrkq9guHyvfMTtmLc1togpZoyqYmyNoITzVTYRJCiXYBIQ3CwFqi83o3JDhX6C0M8XsGIMoQ4OyuRlq1DdZcLkmbgGDX1iIEKNxAcbgTEOqC4ZRaJ6Ub86K7CYFEo8Qo+GBQlQyXBczLZpbloaQ9k1NUz/kD2myBBKxRZpa5hVcQslalatoUxizxAVVrN3CW21bFj9F858Q9dnIRmDyeuybM71uxmH9BNBB1q6zybV7H9s1Ue4PM3/gu/AEbfqfWy2twsAAAAAElFTkSuQmCC';
    }

    /**
     * Gets menu tab for the Debug Bar
     *
     * @return string
     */
    public function getTab()
    {
        if (!$this->auth->hasIdentity()) {
            return 'Not authorized';
        }

        $identity = $this->auth->getIdentity();

        if (is_object($identity)) {
            $username = $this->auth->getIdentity()->{$this->user};
            $role = $this->auth->getIdentity()->{$this->role};
        } else {
            $username = $this->auth->getIdentity();
            $role = '';
        }

        if (!empty($this->callback) && is_callable($this->callback)) {
            $username = call_user_func($this->callback, $username);
        }

        return $username . ' (' . $role . ')';
    }

    /**
     * Gets content panel for the Debug Bar
     *
     * @return string
     */
    public function getPanel()
    {
        if (!$this->auth->hasIdentity()) {
            $html = '<h4>No identity</h4>';
        } else {
            $html = '<h4>Identity</h4>';
            $html .= $this->_cleanData($this->auth->getIdentity());
        }

        return $html;
    }
}
