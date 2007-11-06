<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Entry_Controller extends Backend_Controller 
{
    // {{{ index
    public function index()
    {
        $this->load->component('PList', 'entries');

        $this->entries->setResource($this->Blog->getEntries());
        $this->entries->setLimit(Config::item('limit', NULL, 0));
        $this->entries->addColumn('subject', _("Subject"));        
        $this->entries->addColumn('author', _("Author"));
        $this->entries->addColumn('Blog.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->entries->addColumn('Blog.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);        
        $this->entries->addColumn('Blog.getModifiedBy', _("Modified By"), PList::VIRTUAL_COLUMN);
        $this->entries->addAction('blog/backend/entry/edit/#id#', 'Edit', 'edit_action');
        $this->entries->addAction('blog/backend/entry/delete/#id#', 'Delete', 'delete_action');
        $this->entries->addAction('blog/backend/entry/preview/#id#', 'Preview', 'view_action');        
        // $this->entries->addAction('blog/backend/entry/delete', 'Delete', 'delete_action', PList::GROUP_ACTION);
        $this->entries->setEmptyListMessage(_("There is no entry!"));

        $this->load->view('backend/index');
    }
    // }}}
    // {{{ post_read
    public function post_read()
    {
        $view = $this->load->view('backend/post');

        $view->categories  = $this->Blog->getCategories();
        $view->status_list = $this->Blog->getStatusOptions();
    }
    // }}}
    // {{{ post_write
    public function post_write()
    {
        $this->Blog->createEntry($this->input->post('subject'), 
                                      $this->input->post('entry'), 
                                      $this->input->post('extended_entry'),
                                      'guest',
                                      $this->input->post('status'),
                                      $this->input->post('allow_comments'),
                                      $this->input->post('requires_moderation'),
                                      $this->input->post('category'));

        url::redirect('blog/backend/entry');
    }
    // }}}
    // {{{ post_write_error
    public function post_write_error()
    {
        $this->post_read();
    }
    // }}}
    // {{{ edit_read
    public function edit_read($id)
    {
        $this->global_tabs->setParameter('id', $id);

        $entry = $this->Blog->getEntry($id);        
        
        $data = Array ('categories'  => $this->Blog->getCategories(), 
                       'status_list' => $this->Blog->getStatusOptions());

        $this->load->vars($data);
        $this->load->vars($entry);
        $this->load->view('backend/edit');
    }
    // }}}
    // {{{ edit_read_error
    public function edit_read_error()
    {
        $this->_invalid_request('blog/backend/entry');
    }
    // }}}    
    // {{{ edit_write
    public function edit_write()
    {
        $result = $this->Blog->editEntry($this->input->post('id'),
                                              $this->input->post('subject'), 
                                              $this->input->post('entry', True), 
                                              $this->input->post('extended_entry', True),
                                              'guest',
                                              $this->input->post('status'),
                                              $this->input->post('allow_comments'),
                                              $this->input->post('requires_moderation'),
                                              $this->input->post('category'));
        url::redirect('blog/backend/entry');
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error()
    {
        $this->global_tabs->setParameter('id', $this->input->post('id'));

        $data = Array ('categories'  => $this->Blog->getCategories(), 
                       'status_list' => $this->Blog->getStatusOptions());

        $this->load->vars($data);
        $this->load->view('backend/edit');
    }
    // }}}
    // {{{ delete_read
    public function delete_read($id)
    {
        $this->global_tabs->setParameter('id', $id);

        $data = Array('id'      => $id, 
                      'subject' => $this->Blog->getEntrySubject($id));

        $this->load->vars($data);
        $this->load->view('backend/delete');
    }
    // }}}
    // {{{ delete_read_error
    public function delete_read_error()
    {
        $this->_invalid_request('blog/backend/entry');
    }
    // }}}
    // {{{ delete_write
    public function delete_write()
    {
        $this->Blog->deleteEntry($this->input->post('id'));

        url::redirect('blog/backend/entry');
    }
    // }}}    
    // {{{ delete_write_error
    public function delete_write_error()
    {
        $this->_invalid_request('blog/backend/entry');        
    }
    // }}}
    // {{{ preview
    public function preview($id)
    {
        $this->global_tabs->setParameter('id', $id);        
        
        $this->load->component('PList', 'entry');

        $this->entry->setResource(Array($this->Blog->getEntry($id)));
        $this->entry->addColumn('Blog.getDate', Null, PList::VIRTUAL_COLUMN);        
        // $this->entry->addColumn('subject');
        // $this->entry->addColumn('author');
        // $this->entry->addColumn('entry');
        // $this->entry->addColumn('extended_entry');

        $this->load->vars(Array('extended'  => True, 'entry_uri' => '/blog/backend/entry/preview/#id#'));
        $this->load->view('backend/preview');
    }
    // }}}
    // {{{ preview_error
    public function preview_error()
    {
        $this->_invalid_request('blog/backend/entry');
    }
    // }}}
    // {{{ _check_entry
    protected function _check_entry($id)
    {
        return $this->Blog->hasEntry($id);
    }
    // }}}
}

?>
