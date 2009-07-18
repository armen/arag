<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Peyman Karimi <zeegco@yahoo.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Load the models
        $this->theme_manager = new Thememanager_Model;

        // Default page title
        $this->layout->page_title = _("Theme Manager");

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Theme Manager"));
        $this->global_tabs->addItem(_("Styles"), 'theme_manager/backend');
        $this->global_tabs->addItem(_("Edit"), 'theme_manager/backend/edit', 'theme_manager/backend');

        // Validation Messages
        $this->validation->message('type', _("%s is not a valid file"));
    }
    // }}}
    // {{{ edit_read
    function edit_read()
    {
        $styles = $this->theme_manager->getDefaults();
        foreach($this->theme_manager->get() as $style => $value) {
            isset($styles[$style]) and $styles[$style]['value'] = $value;
        }

        $this->layout->content = new View('backend/edit');
        $this->layout->content->styles  = $styles;
        $this->layout->content->flagsaved = $this->session->get('theme_manager_style_saved');
    }
    // }}}
    // {{{ edit_write
    function edit_write()
    {
        $upload_path    = DOCROOT.'modpub/theme_manager/uploaded/';
        $styles         = array();
        $default_styles = $this->theme_manager->getDefaults();
        foreach($default_styles as $key => $value) {
            if ($default_styles[$key]['type'] == 'color') {
                $temp = $this->input->post($key, Null, true);
                $temp and ($styles[$key] = $temp);
            } else { // It is an image
                $temp = $this->input->post($key, Null, true);
                if (is_array($temp)) {
                    $new_name = str_replace(' ', null, microtime()) . mt_rand(0, 9) . mt_rand(0, 9). '.' . file::extension($temp['name']);
                    ($temp['error'] == UPLOAD_ERR_OK) and upload::save($key, $new_name, $upload_path) and ($styles[$key] = $new_name);
                } 
            }
        }

        // Let's do some clean-ups :) First remove old image files :)
        $default_styles = $this->theme_manager->getDefaults();
        foreach($this->theme_manager->get() as $style => $value) {
            isset($default_styles[$style]) and $default_styles[$style]['type'] == 'file' and unlink($upload_path.$value);
        }

        $this->theme_manager->set($styles);
        $this->session->set_flash('theme_manager_style_saved', true);
        $cache = Cache::instance();
        $cache->delete(APPNAME.'_styles');
        $this->edit_read();
    }
    // }}}
    // {{{ edit_validate_write
    public function edit_validate_write()
    {
        $allowed_image_extensions = Arag_Config::get('config.allowed_image_extensions', '', 'theme_manager', true);
        $default_styles = $this->theme_manager->getDefaults();
        foreach($default_styles as $key => $value) {
            if ($default_styles[$key]['type'] == 'file') {
                $this->validation->name($key, $default_styles[$key]['description'])
                     ->add_rules($key,"upload::type[$allowed_image_extensions]");
            }
        }
        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error()
    {
        $styles = $this->theme_manager->getDefaults();
        foreach($styles as $style => $value) {
            ($styles[$style]['type'] != 'file') and $styles[$style]['value'] = $this->input->post($style, Null, true);;
        }
        $this->layout->content          = new View('backend/edit');
        $this->layout->content->styles  = $styles;
    }
    // }}}
}
