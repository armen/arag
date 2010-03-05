<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Entry_Controller extends Blog_Backend
{
    // {{{ index
    public function index_any()
    {
        $entries = new PList_Component('entries');

        $entries->setResource($this->Blog->getEntries());
        $entries->setLimit(Arag_Config::get('limit', 0));
        $entries->addColumn('subject', _("Subject"));
        $entries->addColumn('author', _("Author"));
        $entries->addColumn('Blog.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $entries->addColumn('Blog.getModifyDate', _("Modify Date"), PList_Component::VIRTUAL_COLUMN);
        $entries->addColumn('Blog.getModifiedBy', _("Modified By"), PList_Component::VIRTUAL_COLUMN);
        $entries->addAction('blog/backend/entry/edit/#id#', 'Edit', 'edit_action');
        $entries->addAction('blog/backend/entry/delete/#id#', 'Delete', 'delete_action');
        $entries->addAction('blog/backend/entry/preview/#id#', 'Preview', 'view_action');

        // $this->entries->addAction('blog/backend/entry/delete', 'Delete', 'delete_action', False, PList_Component::GROUP_ACTION);
        $entries->setEmptyListMessage(_("There is no entry!"));

        $this->layout->content = new View('backend/index');
    }
    // }}}
    // {{{ post
    // {{{ post_read
    public function post_read()
    {
        $this->layout->content = new View('backend/post');

        $this->layout->content->categories  = $this->Blog->getCategories();
        $this->layout->content->status_list = $this->Blog->getStatusOptions();
    }
    // }}}
    // {{{ post_write
    public function post_write()
    {
        $this->Blog->createEntry($this->input->post('subject'),
                                 $this->input->post('entry'),
                                 $this->input->post('extended_entry'),
                                 $this->session->get('user.username'),
                                 $this->input->post('status'),
                                 $this->input->post('allow_comments'),
                                 $this->input->post('requires_moderation'),
                                 $this->input->post('category'));

        url::redirect('blog/backend/entry');
    }
    // }}}
    // {{{ post_validate_write
    public function post_validate_write()
    {
        $this->validation->name('subject', _("Subject"))->add_rules('subject', 'required', 'valid::standard_text')->post_filter('trim', 'subject');
        $this->validation->name('entry', _("Entry Body"))->add_rules('entry', 'required')->post_filter('security::xss_clean', 'entry');
        $this->validation->name('extended_entry', _("Extended Entry"))->post_filter('security::xss_clean', 'extended_entry');
        $this->validation->add_rules('status', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ post_write_error
    public function post_write_error()
    {
        $this->post_read();
    }
    // }}}
    // }}}
    // {{{ edit
    // {{{ edit_read
    public function edit_read($id)
    {
        $this->global_tabs->setParameter('id', $id);

        $entry = $this->Blog->getEntry($id);
        $data  = Array ('categories'  => $this->Blog->getCategories(),
                        'status_list' => $this->Blog->getStatusOptions());

        $this->layout->content = new View('backend/edit', array_merge($data, $entry));
    }
    // }}}
    // {{{ edit_validate_read
    public function edit_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, '_check_entry'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_read_error
    public function edit_read_error()
    {
        $this->_invalid_request('blog/backend/entry', _("Invalid ID"));
    }
    // }}}
    // {{{ edit_write
    public function edit_write()
    {
        $result = $this->Blog->editEntry($this->input->post('id'),
                                         $this->input->post('subject'),
                                         $this->input->post('entry', Null, True),
                                         $this->input->post('extended_entry', Null, True),
                                         $this->session->get('user.username'),
                                         $this->input->post('status'),
                                         $this->input->post('allow_comments'),
                                         $this->input->post('requires_moderation'),
                                         $this->input->post('category'));
        url::redirect('blog/backend/entry');
    }
    // }}}
    // {{{ edit_validate_write
    public function edit_validate_write()
    {
        $this->validation->name('id', _("ID"))->add_rules('id', 'required', 'valid::numeric', array($this, '_check_entry'));
        $this->validation->name('subject', _("Subject"))->add_rules('subject', 'required', 'valid::standard_text')->post_filter('trim', 'subject');
        $this->validation->name('entry', _("Entry Body"))->add_rules('entry', 'required')->post_filter('security::xss_clean', 'entry');
        $this->validation->name('extended_entry', _("Extended Entry"))->post_filter('security::xss_clean', 'extended_entry');
        $this->validation->add_rules('status', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error()
    {
        $this->global_tabs->setParameter('id', $this->input->post('id'));

        $data = Array ('categories'  => $this->Blog->getCategories(),
                       'status_list' => $this->Blog->getStatusOptions());

        $this->layout->content = new View('backend/edit', $data);
    }
    // }}}
    // }}}
    // {{{ delete
    // {{{ delete_read
    public function delete_read($id)
    {
        $this->global_tabs->setParameter('id', $id);

        $data = Array('id'      => $id,
                      'subject' => $this->Blog->getEntrySubject($id));

        $this->layout->content = new View('backend/delete', $data);
    }
    // }}}
    // {{{ delete_validate_read
    public function delete_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, '_check_entry'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_read_error
    public function delete_read_error()
    {
        $this->_invalid_request('blog/backend/entry', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_write
    public function delete_write()
    {
        $this->Blog->deleteEntry($this->input->post('id'));

        url::redirect('blog/backend/entry');
    }
    // }}}
    // {{{ delete_validate_write
    public function delete_validate_write()
    {
        $this->validation->name('id', _("ID"))->add_rules('id', 'required', 'valid::numeric', array($this, '_check_entry'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_write_error
    public function delete_write_error()
    {
        $this->_invalid_request('blog/backend/entry', _("Invalid ID"));
    }
    // }}}
    // }}}
    // {{{ preview
    // {{{ preview_any
    public function preview_any($id)
    {
        $this->global_tabs->setParameter('id', $id);

        $entry = new PList_Component('entry');

        $entry->setResource(Array($this->Blog->getEntry($id)));
        $entry->addColumn('Blog.getDate', Null, PList_Component::VIRTUAL_COLUMN);
        // $entry->addColumn('subject');
        // $entry->addColumn('author');
        // $entry->addColumn('entry');
        // $entry->addColumn('extended_entry');

        $comments = new Comment_Component('comments', $id);

        $this->layout->content = new View('backend/preview', Array('extended'  => True, 'entry_uri' => '/blog/backend/entry/preview/#id#'));
    }
    // }}}
    // {{{ preview_validate_any
    public function preview_validate_any()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, '_check_entry'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ preview_any_error
    public function preview_any_error()
    {
        $this->_invalid_request('blog/backend/entry', _("Invalid ID"));
    }
    // }}}
    // }}}
    // {{{ _check_entry
    public function _check_entry($id)
    {
        return $this->Blog->hasEntry($id);
    }
    // }}}
}

?>
