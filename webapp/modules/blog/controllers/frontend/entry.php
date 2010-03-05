<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once(Arag::find_file('comment', 'libraries', 'controller'));

class Entry_Controller extends Comments_Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Load the model
        $this->Blog = new Blog_Model;

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('standard_text', _("%s should be ordinary text."));
        $this->validation->message('email', _("%s should be a valid email."));
        $this->validation->message('url', _("%s should be a valid url."));

        // Default page title
        $this->layout->page_title = 'Blog';
    }
    // }}}
    // {{{ index
    public function index_any()
    {
        $entry = new PList_Component('entry');
        $entry->setLimit(Arag_Config::get('post_limit', 0));
        $entry->setResource($this->Blog->getEntries(True));
        $entry->addColumn('Blog.getDate', Null, PList_Component::VIRTUAL_COLUMN);

        // Load the Comment component, comment ain't used but just loaded
        $comments = new Comment_Component('comments');

        $this->layout->content = new View('frontend/view', Array('entry_uri' => '/blog/frontend/entry/view/#id#/extended'));
    }
    // }}}
    // {{{ view
    // {{{ view_any
    public function view_any($id, $extended = False)
    {
        $entry = new PList_Component('entry');
        $entry->setResource(Array($this->Blog->getEntry($id, True)));
        $entry->addColumn('Blog.getDate', Null, PList_Component::VIRTUAL_COLUMN);

        $comments = new Comment_Component('comments', $id);
        $comments->showOnlyVerified();

        $data     = Array('extended'  => $extended == 'extended',
                          'entry_uri' => '/blog/frontend/entry/view/#id#/extended');

        $this->layout->content = new View('frontend/view', $data);
    }
    // }}}
    // {{{ view_validate_any
    public function view_validate_any()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, '_check_entry'));
        $this->validation->add_rules(1, 'required', 'valid::alpha');

        return $this->validation->validate();
    }
    // }}}
    // {{{ view_any_error
    public function view_any_error()
    {
        $this->_invalid_request('blog/frontend/entry/index', _("Invalid ID"));
    }
    // }}}
    // }}}
    // {{{ post
    // {{{ post_comment_write
    public function post_comment_write()
    {
        $comment = Model::load('Comment', 'comment');

        $entryId = $this->input->post('reference_id');

        $comment->createComment('blog',
                                $entryId,
                                $this->session->get('user.username'),
                                $this->input->post('comment'),
                                0,
                                0,
                                $this->input->post('name'),
                                $this->input->post('email'),
                                $this->input->post('homepage'));

        $this->view($entryId, 'extended');
    }
    // }}}
    // {{{ post_comment_validate_write
    public function post_comment_validate_write()
    {
        $this->validation->name('reference_id', _("Reference Id"))->add_rules('reference_id', 'required', 'valid::numeric', array($this, '_check_entry'));
        $this->validation->name('name', _("Name"))->add_rules('name', 'required', 'valid::standard_text')->post_filter('trim', 'name');
        $this->validation->name('email', _("Email"))->add_rules('email', 'valid::email_rfc')->post_filter('trim', 'name');
        $this->validation->name('homepage', _("Homepage"))->add_rules('homepage', 'valid::url')->post_filter('trim', 'name');
        $this->validation->name('comment', _("Comment"))->add_rules('comment', 'required')->post_filter('security::xss_clean', 'name');

        return $this->validation->validate();
    }
    // }}}
    // {{{ post_comment_write_error
    public function post_comment_write_error()
    {
        $this->view($this->input->post('reference_id'), Null, 'extended');
    }
    // }}}
    // {{{ post_comment_read
    public function post_comment_read()
    {
        $this->_invalid_request('blog/frontend/entry/index', _("Invalid reference ID"));
    }
    // }}}
    // }}}
    // {{{ _check_entry
    public function _check_entry($id)
    {
        return $this->Blog->hasEntry($id, True);
    }
    // }}}
}
