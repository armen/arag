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
    // {{{ postRead
    function postRead()
    {
        $this->load->library('validation');    
        $this->validation->set_rules(Array('subject' => 'numeric'));



        $data = Array ('categories' => $this->BlogModel->getCategories(), 
                       'status'     => $this->BlogModel->getStatusOptions());

        $this->load->vars($data);
        $this->load->view('backend/post');
    }
    // }}}
    // {{{ postWrite
    function postWrite()
    {
        $this->load->helper('url');

        $this->load->library('validation');
        $this->validation->set_rules($this->_get_rule('post'));
        
        if ($this->validation->run()) {

            $this->BlogModel->createEntry($this->input->post('subject'), 
                                          $this->input->post('entry'), 
                                          $this->input->post('extended_entry'),
                                          'guest',
                                          $this->input->post('status'),
                                          $this->input->post('allow_comments'),
                                          $this->input->post('requires_moderation'),
                                          $this->input->post('category'));

            redirect('blog/backend/index');

        } else {

            $this->validation->set_fields(Array('subject' => $this->input->post('subject')));
        
            $data = Array ('categories' => $this->BlogModel->getCategories(), 
                           'status'     => $this->BlogModel->getStatusOptions());

            $this->load->vars($data);
            $this->load->view('backend/post');        
        }
    }
    // }}}
    // {{{ edit
    function edit($dummy, $id = 0)
    {
        $this->global_tabs->setParameter('id', $id);

        $entry = $this->BlogModel->getEntry($id);

        if (count($entry) == 0) {
            $this->_invalid_request('blog/backend/index');
            return;
        }

        $data = Array ('categories' => $this->BlogModel->getCategories(), 
                       'status'     => $this->BlogModel->getStatusOptions());

        $this->load->vars($data);
        $this->load->vars($entry);
        $this->load->view('backend/edit');
    }
    // }}}
    // {{{ do_edit
    function do_edit()
    {
        $this->load->helper('url');

        $result = $this->BlogModel->editEntry($this->input->post('id'),
                                              $this->input->post('subject'), 
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
    // {{{ delete
    function delete()
    {
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
    function preview()
    {
    }
    // }}}
    // {{{ _get_rule
    function _get_rule($name)
    {
        $validor['post']['subject'] = Array ('rule'           => 'trim|required|min_length[5]|max_length[12]|xss_clean', 
                                             'required_error' => _("You have to enter a number!"));

        return $rules[$name];
    }
    // }}}
}

?>
