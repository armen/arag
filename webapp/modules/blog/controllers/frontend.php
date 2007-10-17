<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend extends Arag_Controller 
{
    // {{{ Constructor
    function Frontend()
    {
        parent::Arag_Controller();

        // Load the model
        $this->load->model('BlogModel');        
       
        // Backend decorator
        $this->load->decorator('frontend/decorator');

        // Default page title
        $this->load->vars(Array('page_title' => 'Blog'));
    }
    // }}}
    // {{{ index
    function index()
    {
        $this->load->helper('url');    

        $this->load->component('PList', 'entry');
        $this->entry->setResource($this->BlogModel->getEntries(True));
        $this->entry->addColumn('BlogModel.getDate', Null, PList::VIRTUAL_COLUMN);

        $this->load->vars(Array('entry_uri' => '/blog/frontend/view/#id#/extended'));
        $this->load->view('frontend/view');    
    }
    // }}}
    // {{{ view
    function view($id, $extended = False)
    {
        $this->load->helper('url');

        $this->load->component('PList', 'entry');
        $this->entry->setResource(Array($this->BlogModel->getEntry($id, True)));
        $this->entry->addColumn('BlogModel.getDate', Null, PList::VIRTUAL_COLUMN);

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
        return $this->BlogModel->hasEntry($id, True);
    }
    // }}}    
}

?>
