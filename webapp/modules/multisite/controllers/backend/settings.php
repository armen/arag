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
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Validation Messages
        $this->validation->message('numeric', _("%s should be numeric"));
        $this->validation->message('required', _("%s is required"));
    }
    // }}}
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
    // {{{ index_validate_write
    public function index_validate_write()
    {
        $this->validation->name('limit', _("Limit"))->pre_filter('trim', 'limit')
             ->add_rules('limit', 'required', 'valid::numeric');

        return $this->validation->validate();
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
                                       MultiSite_Model::BLOCK_URI => _("BLock user and remove URI"),
                                       MultiSite_Model::BLOCK     => _("Just block user"),
                                       MultiSite_Model::URI       => _("Just remove URI")
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
    // {{{ user_blocking_validate_write
    public function user_blocking_validate_write()
    {
        $this->validation->name('block_expire', _("Blocking expire time"))->pre_filter('trim', 'block_expire')
             ->add_rules('block_expire', 'required', 'valid::numeric');

        $this->validation->name('block_counter', _("Blocking attempts"))->pre_filter('trim', 'block_counter')
             ->add_rules('block_counter', 'required', 'valid::numeric');

        return $this->validation->validate();
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
