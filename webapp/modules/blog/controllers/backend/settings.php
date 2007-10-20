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
    function index_read($saved = Null)
    {
        $data          = Array();
        $data['limit'] = $this->config->item('limit');
        $data['saved'] = $saved;

        $this->load->vars($data);        
        $this->load->view('backend/settings');
    }
    // }}}    
    // {{{ index_write
    function index_write()
    {
        $this->load->helper('url');
    
        $this->config->set_item('limit', $this->input->post('limit'));

        redirect('blog/backend/settings/index/saved');
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
