<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Node_Controller extends Help_Backend
{
    // {{{ index
    public function index_any()
    {
        $help  = new Help_Model;
        $nodes = new PList_Component('nodes');

        $nodes->setResource($help->getNodes());
        $nodes->setLimit(Arag_Config::get('limit', 0));
        $nodes->addColumn('subject', _("Subject"));
        $nodes->addColumn('author', _("Author"));
        $nodes->addColumn('Help.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $nodes->addColumn('modified_by', _("Modified By"));
        $nodes->addColumn('Help.getModifyDate', _("Modify Date"), PList_Component::VIRTUAL_COLUMN);
        $nodes->addAction('help/backend/node/edit/#id#', 'Edit', 'edit_action');
        $nodes->addAction('help/backend/node/delete/#id#', 'Delete', 'delete_action');
        $nodes->setEmptyListMessage(_("There is no created node!"));

        $this->layout->content = new View('backend/index');
    }
    // }}}
    // {{{ create
    // {{{ create_read
    public function post_read()
    {
        $this->layout->content = new View('backend/create');

        // $this->layout->content->block_names = ;
    }
    // }}}
    // {{{ create_write
    public function create_write()
    {
        $help->createNode($this->input->post('node'),
                          $this->input->post('subject'),
                          $this->input->post('block_name'),
                          $this->session->get('body'));

        url::redirect('help/backend/node');
    }
    // }}}
    // {{{ create_validate_write
    public function create_validate_write()
    {
        $this->validation->name('title', _("Title"))->add_rules('title', 'valid::standard_text')->post_filter('trim', 'title');
        $this->validation->name('node', _("Node"))->add_rules('node', 'required');
        $this->validation->name('body', _("Body"))->post_filter('security::xss_clean', 'body');
        $this->validation->name('block_name', _("Block Name"))->add_rules('block_name', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ create_write_error
    public function create_write_error()
    {
        $this->post_read();
    }
    // }}}
    // }}}
}
