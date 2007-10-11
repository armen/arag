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
        $this->load->decorator('backend/decorator');

        // Default page title
        $this->load->vars(Array('page_title' => 'Blog'));

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("Blog"));
        $this->global_tabs->addItem(_("Edit Entries"), 'blog/backend/index');
        $this->global_tabs->addItem(_("Edit Entry"), 'blog/backend/edit/%id%', 'blog/backend/index');
        $this->global_tabs->addItem(_("Delete Entry"), 'blog/backend/delete/%id%', 'blog/backend/index');
        $this->global_tabs->addItem(_("Preview Entry"), 'blog/backend/preview/%id%', 'blog/backend/index');
        $this->global_tabs->addItem(_("New Entry"), 'blog/backend/post');
        $this->global_tabs->addItem(_("Categories"), 'blog/backend/categories');
        $this->global_tabs->addItem(_("Settings"), 'blog/backend/settings');
    }
    // }}}
    // {{{ index
    function index()
    {
        $this->load->model('BlogModel');

        $this->load->component('PList', 'entries');

        $this->entries->setResource($this->BlogModel->getEntries());
        $this->entries->setLimit(2);
        $this->entries->addColumn('subject', _("Subject"));        
        $this->entries->addColumn('author', _("Author"));
        $this->entries->addColumn('BlogModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->entries->addColumn('BlogModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);        
        $this->entries->addAction('blog/backend/edit/#id#', 'Edit', 'edit_action');
        $this->entries->addAction('blog/backend/delete/#id#', 'Delete', 'delete_action');
        $this->entries->addAction('blog/backend/preview/#id#', 'Preview', 'view_action');        
        $this->entries->addAction('blog/backend/delete', 'Delete', 'delete_action', PList::GROUP_ACTION);
        $this->entries->setEmptyListMessage(_("There is no entry!"));

        $this->load->view('backend/index');
    }
    // }}}
    // {{{ post
    function post()
    {
        $this->load->model('BlogModel');

        $data = Array ('categories' => $this->BlogModel->getCategories());

        $this->load->vars($data);
        $this->load->view('backend/post');     
    }
    // }}}
    // {{{ do_post
    function do_post()
    {
        
    }
    // }}}
    // {{{ categories
    function categories()
    {
    }
    // }}}
    // {{{ edit
    function edit($dummy, $id = 0)
    {
        $this->global_tabs->setParameter('id', $id);
        $this->load->view('backend/edit'); 
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
    // {{{ preview
    function preview()
    {
    }
    // }}}
}

?>
