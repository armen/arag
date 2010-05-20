<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for creating contacts component
 *
 * @author Sasan Rose      <sasan.rose@gmail.com>
 * @since  PHP 5
 */

class Contacts_Component extends Component
{
    // {{{ Properties
    
    const ADDRESS  = 'address';
    const TEL      = 'tel';
    const LOCATION = 'location';
    const FAX      = 'fax';
    const CELL     = 'cellphone';
    const EMAIL    = 'email';

    private $namespace;
    private $referenceId;
    private $type = Null;
    private $uri;
    private $key;
    private $session;
    private $contacts;
    private $controller;
    private $onlyShow = False;

    // }}}
    // {{{ Constructor
    public function __construct($namespace = Null, $referenceId = Null)
    {
        parent::__construct($namespace);

        $this->session = Session::instance();

        $this->setNamespace($namespace);
        $this->setReferenceId($referenceId);

        $this->setUri();
    }
    // }}}
    // {{{ setReferenceId
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
        $this->session->set_flash('contacts.'.$this->getKey().'.reference_id', $referenceId);
    }
    // }}}
    // {{{ setNamespace
    public function setNamespace($namespace = Null)
    {
        $this->namespace = empty($namespace) ? Router::$module : $namespace; //If namespace is not set, namespace is module name
        $this->session->set_flash('contacts.'.$this->getKey().'.namespace', $this->namespace);
    }
    // }}}
    // {{{ setUri
    public function setUri($uri = Null, $redirect_uri = Null)
    {
        ($redirect_uri == Null) AND $redirect_uri = Router::$current_uri;

        if ($this->session->get('contacts.'.$this->getKey().'.uri')) {
            $this->uri    = $this->session->get_once('contacts.'.$this->getKey().'.controller');
            $redirect_uri = $this->session->get_once('contacts.'.$this->getKey().'.uri');

        } else if ($uri != Null) {
            $this->uri = $uri;

        } else {

            $this->uri  = implode('/', array_diff(Router::$rsegments, Router::$arguments));
            $this->uri  = rtrim($this->uri, Router::$method.'/'); // Append a slash to $destination
            $this->uri .= isset($this->referenceId) ? '/contacts_edit' : '/contacts_add';
        }

        $this->session->set_flash('contacts.'.$this->getKey().'.uri', $redirect_uri); // Redirect back here baby
        $this->session->set_flash('contacts.'.$this->getKey().'.controller', $this->uri); // My address, for link to attachments
    }
    // }}}
    // {{{ getReferenceId
    public function getReferenceId()
    {
        return $this->referenceId;
    }
    // }}}
    // {{{ getNamespace
    public function getNamespace()
    {
        return $this->namespace;
    }
    // }}}
    // {{{ getUri
    public function getUri()
    {
        return $this->uri;
    }
    // }}}
    // {{{ getContacts
    public function getContacts()
    {
        return $this->contacts;
    }
    // }}}
    // {{{ getKey
    public function getKey()
    {
        if (!$this->key) {
            // In case of validation error try to fetch already existed key from post
            $this->key = Input::instance()->post('key', Null);
        }

        if (!$this->key) {
            $this->key = sha1(microtime());
        }

        return $this->key;
    }
    // }}}
    // {{{ build
    public function build()
    {
        if (isset($this->referenceId)) {
            $contacts        = new Contacts_Model;
            $this->contacts = $contacts->getContacts($this->namespace, $this->referenceId);
        } else {
            $this->contacts = Array();
        }
    }
    // }}}
    // {{{ getTypes
    public function getTypes()
    {
        if (!isset($this->type)) {
            $class  = new ReflectionClass('Contacts_Component');
            $consts = $class->getConstants();
            $types  = Array();

            foreach ($consts as $const) {
                $types[$const] = _($const);
            }

            return $types;
        }

        return $this->type;
    }
    // }}}
    // {{{ onlyShow
    public function onlyShow($onlyShow = Null)
    {
        if (is_bool($onlyShow)) {
            $this->onlyShow = $onlyShow;
        }

        return $this->onlyShow;
    }
    // }}}
}

?>
