<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Armen Baghumian <armen@OpenSourceClub.org>                     |
// |          Sasan Rose <sasan.rose@gmail.com>                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for creating comment component
 *
 * @author Armen Baghumian <armen@OpenSourceClub.org>
 * @author Sasan Rose      <sasan.rose@gmail.com>
 * @since  PHP 5
 */

class Comment_Component extends Component
{
    // {{{ Properties

    private $namespace;
    private $referenceId;
    private $onlyComment = True;
    private $title;
    private $comments = Null;
    private $uri;
    private $verify_uri;
    private $key;
    private $session;
    private $controller;
    private $editable;
    private $showOnlyVerified = False;

    // }}}
    // {{{ Constructor
    public function __construct($namespace = Null, $referenceId = Null)
    {
        parent::__construct($namespace);

        $this->session = Session::instance();

        $this->setNamespace($namespace);
        $this->setReferenceId($referenceId);
        $this->onlyComment(False);

        $this->setUri();
        $this->setVerifyUri();

        $this->editable = in_array('backend', Router::$rsegments);

        $this->title = _("Comments");
    }
    // }}}
    // {{{ setReferenceId
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
        $this->session->set_flash('comment.'.$this->getKey().'.reference_id', $referenceId);
    }
    // }}}
    // {{{ setNamespace
    public function setNamespace($namespace = Null)
    {
        $this->namespace = empty($namespace) ? Router::$module : $namespace; //If namespace is not set, namespace is module name
        $this->session->set_flash('comment.'.$this->getKey().'.namespace', $this->namespace);
    }
    // }}}
    // {{{ setUri
    public function setUri($uri = Null, $redirect_uri = Null)
    {
        ($redirect_uri == Null) AND $redirect_uri = Router::$current_uri;

        if ($uri != Null) {
            $this->uri = $uri;

        } else if ($this->session->get('comment.'.$this->getKey().'.uri')) {
            $this->uri    = $this->session->get_once('comment.'.$this->getKey().'.controller');
            $redirect_uri = $this->session->get_once('comment.'.$this->getKey().'.uri');

        } else {

            $this->uri  = implode('/', array_diff(Router::$rsegments, Router::$arguments));
            $this->uri  = rtrim($this->uri, Router::$method.'/'); // Append a slash to $destination
            $this->uri .= '/comment_add';
        }

        $this->session->set_flash('comment.'.$this->getKey().'.uri', $redirect_uri); // Redirect back here baby
        $this->session->set_flash('comment.'.$this->getKey().'.controller', $this->uri); // My address, for link to attachments
    }
    // }}}
    // {{{ setVerifyUri
    public function setVerifyUri($uri = Null, $redirect_uri = Null)
    {
        ($redirect_uri == Null) AND $redirect_uri = Router::$current_uri;

        if ($uri != Null) {
            $this->verify_uri = $uri;

        } else if ($this->session->get('comment.'.$this->getKey().'.verify_uri')) {
            $this->verify_uri = $this->session->get_once('comment.'.$this->getKey().'.verify_controller');
            $redirect_uri     = $this->session->get_once('comment.'.$this->getKey().'.verify_uri');

        } else {

            $this->verify_uri  = implode('/', array_diff(Router::$rsegments, Router::$arguments));
            $this->verify_uri  = rtrim($this->verify_uri, Router::$method.'/'); // Append a slash to $destination
            $this->verify_uri .= '/comment_verify';
        }

        $this->session->set_flash('comment.'.$this->getKey().'.verify_uri', $redirect_uri); // Redirect back here baby
        $this->session->set_flash('comment.'.$this->getKey().'.verify_controller', $this->verify_uri); // My address, for link to attachments
    }
    // }}}
    // {{{ setTitle
    public function setTitle($title = Null)
    {
        $this->title = $title;
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
    // {{{ getVerifyUri
    public function getVerifyUri()
    {
        return $this->verify_uri;
    }
    // }}}
    // {{{ getComments
    public function getComments()
    {
        return $this->comments;
    }
    // }}}
    // {{{ getTitle
    public function getTitle()
    {
        return $this->title;
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
    // {{{ onlyComment
    public function onlyComment($onlyComment = Null)
    {
        if (is_bool($onlyComment)) {
            $this->onlyComment = $onlyComment;
        }

        return $this->onlyComment;
    }
    // }}}
    // {{{ isEditable
    public function isEditable()
    {
        return $this->editable;
    }
    // }}}
    // {{{ build
    public function build()
    {
        $comment        = new Comment_Model;
        $this->comments = $comment->getComments($this->namespace, $this->referenceId, $this->showOnlyVerified ? 1 : Null);
    }
    // }}}
    // {{{ showOnlyVerified
    public function showOnlyVerified()
    {
        $this->showOnlyVerified = True;
    }
    // }}}
}

?>
