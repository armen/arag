<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Armen Baghumian <armen@OpenSourceClub.org>                     |
// |          Sasan Rose <sasan.rose@gmail.com>                              |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for create paginated list
 *
 * @author Armen Baghumian <armen@OpenSourceClub.org>
 * @author Sasan Rose      <sasan.rose@gmail.com>
 * @since  PHP 5
 */

class Comment_Component extends Component
{
    // {{{ Properties

    private $module;
    private $referenceId;
    private $postUri;
    private $onlyComment;
    private $title;
    private $comments = Null;

    // }}}
    // {{{ Constructor
    public function __construct($namespace = Null)
    {
        parent::__construct($namespace);

        $this->setModule();
        $this->setPostUri();
        $this->onlyComment(False);

        $this->title = _("Comments");
    }
    // }}}
    // {{{ setReferenceId
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
    }
    // }}}
    // {{{ setModule
    public function setModule($module = Null)
    {
        $this->module = empty($module) ? Router::$module : $module;
    }
    // }}}
    // {{{ setPostUri
    public function setPostUri($uri = Null)
    {
        $this->postUri = empty($uri) ? Router::$current_uri : $uri;
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
    // {{{ getModule
    public function getModule()
    {
        return $this->module;
    }
    // }}}
    // {{{ getPostUri
    public function getPostUri()
    {
        return $this->postUri;
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
    // {{{ onlyComment
    public function onlyComment($onlyComment = Null)
    {
        if (is_bool($onlyComment)) {
            $this->onlyComment = $onlyComment;
        }

        return $this->onlyComment;
    }
    // }}}
    // {{{ build
    public function build()
    {
        $comment        = new Comment_Model;
        $this->comments = $comment->getComments($this->module, $this->referenceId);
    }
    // }}}
}

?>
