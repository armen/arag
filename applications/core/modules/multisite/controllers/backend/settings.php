<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Settings_Controller extends Backend_Controller 
{

    // {{{ index_read
    public function index_read()
    {   
        $data          = Array();
        $data['limit'] = Arag_Config::get("limit");
        $data['saved'] = $this->session->get_once('multi_site_settings_limit_saved');

        $this->layout->content = new View('backend/settings_limit', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {

        Arag_Config::set('limit', $this->input->post('limit'));

        $this->session->set('multi_site_settings_limit_saved', true);

        $this->index_read();

    }
    // }}}
    // {{{ index_write_error
    public function index_write_error()
    {
        $this->index_read();
    }
    // }}}
    // {{{ privileges_read
    public function privileges_read()
    {   
        $data = Array();
        
        if (Arag_Config::get("adminpri") != NULL) {
            $data['adminpri'] = implode(" ", Arag_Config::get("adminpri"));
        }

        if (Arag_Config::get("adminpri") != NULL) {
            $data['anonypri'] = implode(" ", Arag_Config::get("anonypri"));
        }

        $data['saved'] = $this->session->get_once('multi_site_settings_privileges_saved');

        $this->layout->content = new View('backend/settings_privileges', $data);
    }
    // }}}
    // {{{ privileges_write
    public function privileges_write()
    {
        Arag_Config::set('adminpri', array_unique(explode(" ", preg_replace('/\s+/', ' ', trim($this->input->post('adminpri', true))))));
        Arag_Config::set('anonypri', array_unique(explode(" ", preg_replace('/\s+/', ' ', trim($this->input->post('anonypri', true))))));

        $this->session->set('multi_site_settings_privileges_saved', true);

        $this->privileges_read();
    }
    // }}}
    // {{{ privileges_write_error
    public function privileges_write_error()
    {
        $this->privileges_read();
    }
    // }}}
    // {{{ user_blocking_read
    public function user_blocking_read()
    {   
        $data                  = Array();
        $data['block_expire']  = Arag_Config::get("verify_block_expire");
        $data['block_counter'] = Arag_Config::get("verify_block_counter");
        $data['saved']         = $this->session->get_once('multisite_settings_user_blocking_saved');
        $data['blockoptions']  = array(
                                       MultiSite_Model::BLOCK_URI => "BLock user and remove URI",
                                       MultiSite_Model::BLOCK     => "Just block user",
                                       MultiSite_Model::URI       => "Just remove URI"
                                      );
        if (arag_config::get('verify_block_action', 6) & Multisite_Model::BLOCK) {
            $data['blockselected'] = MultiSite_Model::BLOCK;
        } else if (Arag_Config::get('verify_block_action', 6) & MultiSite_Model::URI) {
            $data['blockselected'] = MultiSite_Model::URI;
        }

        if ((Arag_Config::get('verify_block_action', 6) & MultiSite_Model::BLOCK) &&
            (Arag_Config::get('verify_block_action', 6) & MultiSite_Model::URI)) {
            $data['blockselected'] = MultiSite_Model::BLOCK_URI;
        }

        $this->layout->content = new View('backend/settings_user_blocking', $data);
    }
    // }}}
    // {{{ user_blocking_write
    public function user_blocking_write()
    {

        Arag_Config::set('verify_block_expire', $this->input->post('block_expire'));
        Arag_Config::set('verify_block_counter', $this->input->post('block_counter'));
        Arag_Config::set('verify_block_action', $this->input->post('block_action'));

        $this->session->set('multisite_settings_user_blocking_saved', true);

        $this->user_blocking_read();

    }
    // }}}
    // {{{ user_blocking_write_error
    public function user_blocking_write_error()
    {
        $this->user_blocking_read();
    }
    // }}}
}
?>
