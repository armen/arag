<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Entry extends Backend 
{
    // {{{ index
    function index()
    {
        $this->load->model('configuration');
        $this->load->component('PList', 'entries');

        $this->entries->setResource($this->BlogModel->getEntries());
        $this->entries->setLimit($this->config->item('limit', NULL, 0));
        $this->entries->addColumn('subject', _("Subject"));        
        $this->entries->addColumn('author', _("Author"));
        $this->entries->addColumn('BlogModel.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->entries->addColumn('BlogModel.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);        
        $this->entries->addColumn('BlogModel.getModifiedBy', _("Modified By"), PList::VIRTUAL_COLUMN);
        $this->entries->addAction('blog/backend/entry/edit/#id#', 'Edit', 'edit_action');
        $this->entries->addAction('blog/backend/entry/delete/#id#', 'Delete', 'delete_action');
        $this->entries->addAction('blog/backend/entry/preview/#id#', 'Preview', 'view_action');        
        // $this->entries->addAction('blog/backend/entry/delete', 'Delete', 'delete_action', PList::GROUP_ACTION);
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

        redirect('blog/backend/entry');
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
        $this->_invalid_request('blog/backend/entry');
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
        redirect('blog/backend/entry');
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
        $this->load->view('backend/delete');
    }
    // }}}
    // {{{ delete_read_error
    function delete_read_error()
    {
        $this->_invalid_request('blog/backend/entry');
    }
    // }}}
    // {{{ delete_write
    function delete_write()
    {
        $this->load->helper('url');
        
        $this->BlogModel->deleteEntry($this->input->post('id'));

        redirect('blog/backend/entry');
    }
    // }}}    
    // {{{ delete_write_error
    function delete_write_error()
    {
        $this->_invalid_request('blog/backend/entry');        
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

        $this->load->vars(Array('extended'  => True, 'entry_uri' => '/blog/backend/entry/preview/#id#'));
        $this->load->view('backend/preview');
    }
    // }}}
    // {{{ preview_error
    function preview_error()
    {
        $this->_invalid_request('blog/backend/entry');
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
