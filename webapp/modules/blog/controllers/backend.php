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

        // Load the model
        $this->load->model('BlogModel');        
       
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
        $this->load->component('PList', 'entries');

        $this->entries->setResource($this->BlogModel->getEntries());
        $this->entries->setLimit(2);
        $this->entries->addColumn('subject', _("Subject"));        
        $this->entries->addColumn('author', _("Author"));
        $this->entries->addColumn('BlogModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->entries->addColumn('BlogModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);        
        $this->entries->addColumn('BlogModel.getModifiedBy', _("Modified By"), PList::VIRTUAL_COLUMN);
        $this->entries->addAction('blog/backend/edit/#id#', 'Edit', 'edit_action');
        $this->entries->addAction('blog/backend/delete/#id#', 'Delete', 'delete_action');
        $this->entries->addAction('blog/backend/preview/#id#', 'Preview', 'view_action');        
        $this->entries->addAction('blog/backend/delete', 'Delete', 'delete_action', PList::GROUP_ACTION);
        $this->entries->setEmptyListMessage(_("There is no entry!"));

        $this->load->view('backend/index');
    }
    // }}}
    // {{{ post_read
    function post_read()
    {
        $data = Array ('categories'  => $this->BlogModel->getCategories(), 
                       'status_list' => $this->BlogModel->getStatusOptions());

        $this->load->vars($data);
        $this->load->view('backend/post');
    }
    // }}}
    // {{{ post_write
    function post_write()
    {
        $this->load->helper('url');

        $this->BlogModel->createEntry($this->input->post('subject'), 
                                      $this->input->post('entry'), 
                                      $this->input->post('extended_entry'),
                                      'guest',
                                      $this->input->post('status'),
                                      $this->input->post('allow_comments'),
                                      $this->input->post('requires_moderation'),
                                      $this->input->post('category'));

        redirect('blog/backend/index');
    }
    // }}}
    // {{{ post_write_error
    function post_write_error()
    {
        $this->post_read();
    }
    // }}}
    // {{{ edit_read
    function edit_read($id)
    {
        $this->global_tabs->setParameter('id', $id);

        $entry = $this->BlogModel->getEntry($id);        
        
        $data = Array ('categories'  => $this->BlogModel->getCategories(), 
                       'status_list' => $this->BlogModel->getStatusOptions());

        $this->load->vars($data);
        $this->load->vars($entry);
        $this->load->view('backend/edit');
    }
    // }}}
    // {{{ edit_read_error
    function edit_read_error()
    {
        $this->_invalid_request('blog/backend/index');
    }
    // }}}    
    // {{{ edit_write
    function edit_write()
    {
        $this->load->helper('url');

        $result = $this->BlogModel->editEntry($this->input->post('id'),
                                              $this->input->post('subject'), 
                                              $this->input->post('entry', True), 
                                              $this->input->post('extended_entry', True),
                                              'guest',
                                              $this->input->post('status'),
                                              $this->input->post('allow_comments'),
                                              $this->input->post('requires_moderation'),
                                              $this->input->post('category'));
        redirect('blog/backend/index');
    }
    // }}}
    // {{{ edit_write_error
    function edit_write_error()
    {
        $this->global_tabs->setParameter('id', $this->input->post('id'));

        $data = Array ('categories'  => $this->BlogModel->getCategories(), 
                       'status_list' => $this->BlogModel->getStatusOptions());

        $this->load->vars($data);
        $this->load->view('backend/edit');
    }
    // }}}
    // {{{ delete_read
    function delete_read($id)
    {
        $this->global_tabs->setParameter('id', $id);

        $data = Array('id'      => $id, 
                      'subject' => $this->BlogModel->getEntrySubject($id));

        $this->load->vars($data);
        $this->load->view('backend/delete.tpl');        
    }
    // }}}
    // {{{ delete_read_error
    function delete_read_error()
    {
        $this->_invalid_request('blog/backend/index');
    }
    // }}}
    // {{{ delete_write
    function delete_write()
    {
        $this->load->helper('url');
        
        $this->BlogModel->deleteEntry($this->input->post('id'));

        redirect('blog/backend/index');        
    }
    // }}}    
    // {{{ delete_write_error
    function delete_write_error()
    {
        $this->_invalid_request('blog/backend/index');        
    }
    // }}}
    // {{{ categories
    function categories()
    {
    }
    // }}}    
    // {{{ settings
    function settings()
    {
    }
    // }}}    
    // {{{ preview
    function preview($id)
    {
        $this->global_tabs->setParameter('id', $id);        
        $this->load->helper('url');    

        $this->load->component('PList', 'entry');

        $this->entry->setResource(Array($this->BlogModel->getEntry($id)));
        $this->entry->addColumn('BlogModel.getDate', Null, PList::VIRTUAL_COLUMN);        
        // $this->entry->addColumn('subject');
        // $this->entry->addColumn('author');
        // $this->entry->addColumn('entry');
        // $this->entry->addColumn('extended_entry');

        $this->load->vars(Array('extended'  => True, 'entry_uri' => '/blog/backend/preview/#id#'));
        $this->load->view('backend/preview');
    }
    // }}}
    // {{{ preview_error
    function preview_error()
    {
        $this->_invalid_request('blog/backend/index');
    }
    // }}}
    // {{{ _check_entry
    function _check_entry($id)
    {
        return $this->BlogModel->hasEntry($id);
    }
    // }}}
}

?>
