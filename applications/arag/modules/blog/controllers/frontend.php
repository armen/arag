<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller 
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Load the model
        $this->load->model('Blog');
       
        // Default page title
        $this->layout->page_title = 'Blog';

    }
    // }}}
    // {{{ index
    public function index()
    {
    
        $this->load->component('PList', 'entry');
        $this->entry->setLimit(Arag_Config::get('post_limit', 0));
        $this->entry->setResource($this->Blog->getEntries(True));
        $this->entry->addColumn('Blog.getDate', Null, PList::VIRTUAL_COLUMN);

        // Load the Comment component, comment ain't used but just loaded
        $this->load->component('Comment', 'comments');

        $this->layout->content = new View('frontend/view', Array('entry_uri' => '/blog/frontend/view/#id#/extended'));    
    }
    // }}}
    // {{{ view
    public function view($id, $extended = False)
    {
        $this->load->component('PList', 'entry');
        $this->entry->setResource(Array($this->Blog->getEntry($id, True)));
        $this->entry->addColumn('Blog.getDate', Null, PList::VIRTUAL_COLUMN);

        $this->load->component('Comment', 'comments');
        $this->comments->setReferenceId($id);
        $this->comments->setPostUri('blog/frontend/post_comment');

        $data = Array('extended'  => $extended == 'extended', 
                      'entry_uri' => '/blog/frontend/view/#id#/extended');

        $this->layout->content = new View('frontend/view', $data);
    }
    // }}}
    // {{{ view_error
    public function view_error()
    {
        $this->_invalid_request('blog/frontend/index');
    }
    // }}}
    // {{{ post_comment_write
    public function post_comment_write()
    {
        $this->load->model('Comment', Null, 'comment');

        $entryId = $this->input->post('reference_id');

        $this->Comment->createComment('blog', 
                                      $entryId,
                                      $this->session->get('username'),
                                      $this->input->post('comment'),
                                      0,
                                      0,
                                      $this->input->post('name'),
                                      $this->input->post('email'),
                                      $this->input->post('homepage'));

        $this->view($entryId, 'extended');
    }
    // }}}
    // {{{ post_comment_read
    public function post_comment_read()
    {
        $this->_invalid_request('blog/frontend/index');    
    }
    // }}}    
    // {{{ post_comment_write_error
    public function post_comment_write_error()
    {
        $this->view($this->input->post('reference_id'), 'extended');
    }
    // }}}
    // {{{ _check_entry
    public function _check_entry($id)
    {
        return $this->Blog->hasEntry($id, True);
    }
    // }}}    
}

?>
