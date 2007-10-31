<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Settings extends Backend 
{
    // {{{ Constructor
    function Settings()
    {
        parent::Backend();
    }
    // }}}
    // {{{ index_read
    function index_read()
    {
        $data               = Array();
        $data['limit']      = $this->config->item('limit');
        $data['post_limit'] = $this->config->item('post_limit');
        $data['saved']      = $this->session->userdata('configuration_saved');

        // unset configuration_set 
        $this->session->unset_userdata('configuration_saved');        

        $this->load->vars($data);        
        $this->load->view('backend/settings');
    }
    // }}}    
    // {{{ index_write
    function index_write()
    {
        $this->load->helper('url');
    
        $this->config->set_item('limit', $this->input->post('limit'));
        $this->config->set_item('post_limit', $this->input->post('post_limit'));

        $this->session->set_userdata('configuration_saved', True);

        redirect('blog/backend/settings/index');
    }
    // }}}
    // {{{ index_write_error
    function index_write_error()
    {
        $this->index_read();
    }
    // }}}
}

?>
