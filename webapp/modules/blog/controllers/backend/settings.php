<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Settings_Controller extends Blog_Backend
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ index_read
    public function index_read()
    {
        $data               = Array();
        $data['limit']      = Arag_Config::get('limit', 0);
        $data['post_limit'] = Arag_Config::get('post_limit', 0);
        $data['saved']      = $this->session->get_once('configuration_saved');

        $this->layout->content = new View('backend/settings', $data);
    }
    // }}}    
    // {{{ index_write
    public function index_write()
    {
        Arag_Config::set('limit', $this->input->post('limit'));
        Arag_Config::set('post_limit', $this->input->post('post_limit'));

        $this->session->set('configuration_saved', True);

        url::redirect('blog/backend/settings/index');
    }
    // }}}
    // {{{ index_validate_write
    public function index_validate_write()
    {
        $this->validation->name('limit', _("Limit"))->add_rules('limit', 'valid::numeric');
        $this->validation->name('post_limit', _("Post Limit"))->add_rules('post_limit', 'valid::numeric');
        
        return $this->validation->validate();
    }
    // }}}
    // {{{ index_write_error
    public function index_write_error()
    {
        $this->index_read();
    }
    // }}}
}

?>
