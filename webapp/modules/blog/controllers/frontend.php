<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller 
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Load the model
        $this->load->model('Blog');
       
        // Default page title
        $this->layout->page_title = 'Blog';
    }
    // }}}
    // {{{ index
    public function index()
    {
        $this->load->component('PList', 'entry');
        $this->entry->setLimit(Arag_Config::get('post_limit', 0));
        $this->entry->setResource($this->Blog->getEntries(True));
        $this->entry->addColumn('Blog.getDate', Null, PList::VIRTUAL_COLUMN);

        $this->layout->content = new View('frontend/view', Array('entry_uri' => '/blog/frontend/view/#id#/extended'));    
    }
    // }}}
    // {{{ view
    public function view($id, $extended = False)
    {
        $this->load->component('PList', 'entry');
        $this->entry->setResource(Array($this->Blog->getEntry($id, True)));
        $this->entry->addColumn('Blog.getDate', Null, PList::VIRTUAL_COLUMN);

        $data = Array('extended'  => $extended == 'extended', 
                      'entry_uri' => '/blog/frontend/view/#id#/extended');

        $this->layout->content = new View('frontend/view', $data);
    }
    // }}}
    // {{{ view_error
    public function view_error()
    {
        $this->_invalid_request('blog/frontend/index');
    }
    // }}}
    // {{{ _check_entry
    public function _check_entry($id)
    {
        return $this->Blog->hasEntry($id, True);
    }
    // }}}    
}

?>
