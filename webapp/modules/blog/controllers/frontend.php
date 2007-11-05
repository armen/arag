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
       
        // Backend decorator
        $this->load->decorator('frontend/decorator');

        // Default page title
        $this->decorator->page_title = 'Blog';
    }
    // }}}
    // {{{ index
    function index()
    {
        $this->load->component('PList', 'entry');
        $this->entry->setLimit(Config::item('post_limit', NULL, 0));        
        $this->entry->setResource($this->Blog->getEntries(True));
        $this->entry->addColumn('Blog.getDate', Null, PList::VIRTUAL_COLUMN);

        $this->load->vars(Array('entry_uri' => '/blog/frontend/view/#id#/extended'));
        $this->load->view('frontend/view');    
    }
    // }}}
    // {{{ view
    function view($id, $extended = False)
    {
        $this->load->component('PList', 'entry');
        $this->entry->setResource(Array($this->Blog->getEntry($id, True)));
        $this->entry->addColumn('Blog.getDate', Null, PList::VIRTUAL_COLUMN);

        $this->load->vars(Array('extended'  => $extended == 'extended', 
                                'entry_uri' => '/blog/frontend/view/#id#/extended'));
        $this->load->view('frontend/view');
    }
    // }}}
    // {{{ view_error
    function view_error()
    {
        $this->_invalid_request('blog/frontend/index');
    }
    // }}}
    // {{{ _check_entry
    function _check_entry($id)
    {
        return $this->Blog->hasEntry($id, True);
    }
    // }}}    
}

?>
