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

class Comment extends Component
{
    // {{{ Properties
    
    private $module;
    private $referenceId;
    private $comments = Null;

    // {{{ Constructor 
    public function __construct($namespace)
    {
        parent::__construct($namespace);

        $this->setModule();
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
    // {{{ getComments
    public function getComments()
    {
        return $this->comments;
    }
    // }}}
    // {{{ build
    public function build()
    {
        $comment        = Model::load('Comment', 'comment');
        $this->comments = $comment->getComments($this->module, $this->referenceId);
    }
    // }}}
}

?>
