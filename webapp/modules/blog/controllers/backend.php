<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend extends Arag_Controller 
{
    // {{{ Constructor
    function Backend()
    {
        parent::Arag_Controller();
       
        // Backend decorator
        $this->load->decorator('backend_decorator');

        // Default page title
        $this->load->vars(Array('page_title' => 'Blog'));

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle('Blog');
        $this->global_tabs->addItem('Edit Entries', 'blog/backend/index');
        $this->global_tabs->addItem('New Entry', 'blog/backend/post');
        $this->global_tabs->addItem('Categories', 'blog/backend/categories');
        $this->global_tabs->addItem('Settings', 'blog/backend/settings');
        $this->global_tabs->addItem('Preview', 'blog/frontend');
    }
    // }}}
    // {{{ index
    function index()
    {
        $this->load->model('BlogModel');

        $this->load->component('PList', 'entries');

        $this->entries->setResource($this->BlogModel->getEntries());
        $this->entries->setLimit(2);
        $this->entries->addColumn('subject', 'Subject');        
        $this->entries->addColumn('author', 'Author');
        $this->entries->addColumn('BlogModel.getDate', 'Create Date', PList::VIRTUAL_COLUMN);
        $this->entries->addAction('blog/backend/edit/#id#', 'Edit', 'edit_action');
        $this->entries->addAction('blog/backend/delete/#id#', 'Delete', 'delete_action');
        $this->entries->addAction('blog/backend/edit', 'Edit', 'edit_action', PList::GROUP_ACTION);
        $this->entries->addAction('blog/backend/delete', 'Delete', 'delete_action', PList::GROUP_ACTION);

        $this->load->view('index');
    }
    // }}}
    // {{{ post
    function post()
    {
    }
    // }}}
    // {{{ categories
    function categories()
    {
    }
    // }}}
    // {{{ edit
    function edit()
    {
    }
    // }}}
    // {{{ delete
    function delete()
    {
    }
    // }}}
    // {{{ settings
    function settings()
    {
    }
    // }}}    
}

?>
