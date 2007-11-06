<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Settings_Controller extends Backend_Controller 
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
        $data['saved']      = $this->session->get('configuration_saved');

        // unset configuration_set 
        $this->session->del('configuration_saved');        

        $this->load->view('backend/settings', $data);
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
    // {{{ index_write_error
    public function index_write_error()
    {
        $this->index_read();
    }
    // }}}
}

?>
