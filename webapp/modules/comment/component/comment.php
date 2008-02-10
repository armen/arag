<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

/*
 * Class for create paginated list
 * 
 * @author  Armen Baghumian <armen@OpenSourceClub.org>
 * @since   PHP 5
 */

class Comment_Component extends Component
{
    // {{{ Properties
    
    private $module;
    private $referenceId;
    private $postUri;
    private $onlyComment;
    private $comments = Null;

    // }}}
    // {{{ Constructor 
    public function __construct($namespace)
    {
        parent::__construct($namespace);

        $this->setModule();
        $this->setPostUri();
        $this->onlyComment(False);
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
