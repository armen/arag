<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// |         Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once "backend.php";

class Install_Controller extends Backend_Controller 
{
    // {{{ index_read
    public function index_read()
    {   
        $show_form = true;
        $messages  = array();
        $settings  = Arag_Config::get('email_settings', False, 'core');

        if (!$settings || !isset($settings['smtpserver']) || !isset($settings['sender']) || !isset($settings['smtpport'])) {
            $messages[] = _("You should first set your email's SMTP settings in the core settings to continue.");
            $show_form  = false;
        }

        if (!is_writeable(DOCROOT . 'sites') || !is_dir(DOCROOT . 'sites')) {
            $messages[] = _("Your 'public_html/sites' directory doesn't exist or isn't writeable.");
            $show_form  = false;
        }

        if (!is_writeable(APPPATH . 'config/sites') || !is_dir(APPPATH . 'config/sites')) {
            $messages[] = _("Your 'config/sites' directory doesn't exist or isn't writeable.");
            $show_form  = false;
        }

        // We don't have to install these modules
        $excludeModules = array
        (
            'user',
            'multisite',
            'ta_locator'
        );        

        $data = array('modules'   => $this->MultiSite->getModules($excludeModules),
                      'messages'  => $messages,
                      'show_form' => $show_form);

        $this->layout->content = new View('backend/install', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        // Load the installation model
        $this->load->model('Installation', 'CoreInstallation', 'core');

        $appname    = $this->input->post('appname', true);
        $email      = $this->input->post('email', true);
        $modules    = $this->input->post('modules', true);
        $author     = $this->session->get('user.username');
        $anonypri   = serialize(Arag_Config::get('anonypri', NULL));
        $adminpri   = serialize(Arag_Config::get('adminpri', NULL));
        $verify_uri = $this->MultiSite->generateVerifyUri(10);
        $password   = strtolower(text::random('alnum', Arag_Config::get('passlength', 8, 'user')));
        $username   = $appname.'_admin';

        // Get next database data source name and id
        $this->MultiSite->getNextDB($DSN, $databaseID);

        // Set the DSN and tablePrefix
        $tablePrefix = str_replace('.', '_', $appname) . '_';
        $this->CoreInstallation->setTablePrefix($tablePrefix);
        $this->CoreInstallation->setDSN($DSN);        
        
        $this->Applications->addApp($appname, $author, 'anonymous', $databaseID);      // Create the application
        $this->Groups->newGroup($appname, 'admin', $author, $adminpri);         // Create admin group of this application
        $this->Groups->newGroup($appname, 'anonymous', $author, $anonypri);     // Create anonymous group of this application
        $this->Users->createUser($appname, $email, NULL, NULL, 'admin', $username, $password, $author, $verify_uri, 0);

        $base_tpl_path = APPPATH . 'modules/multisite/templates';

        // Create symlink in to the /var/www, this is just for local testing
        @symlink(DOCROOT . 'sites/'.$appname, '/var/www/'.$appname);

        // Create directory of application
        $this->CoreInstallation->createDirectory(DOCROOT . 'sites/'.$appname);

        // Create scripts symbolic link
        symlink(DOCROOT . '/scripts', DOCROOT . 'sites/'.$appname.'/scripts');

        // Create index file
        $this->CoreInstallation->createFromTemplate(DOCROOT . 'sites/'.$appname.'/index.php', 'index.php', Array(), $base_tpl_path);

        // Create config file
        $parameters = Array('dsn' => $DSN, 'table_prefix' => $tablePrefix, 'parent_base_url' => url::base());
        $this->CoreInstallation->createFromTemplate(APPPATH . 'config/sites/'.$appname.'.php', 'config.php', $parameters, $base_tpl_path);

        // Core module is required
        if (empty($modules) || !is_array($modules) || !in_array('core', $modules)) {
            $modules[] = 'core';
        }
        
        foreach ($modules as $module) {
            
            $Installation = ucfirst($module) . 'Installation';
            $Installation = Model::load($Installation, $module);
            
            if (is_callable(array($Installation, 'install'))) {
                
                // Change include_once to module path
                Config::set('core.modules', Array(APPPATH . 'modules/' . $module));
                
                $Installation->install($this->CoreInstallation);

                // Reset the include_paths
                Config::set('core.modules', Array(APPPATH . 'modules/' . Router::$module));
            }
        }

        // Send an email to verify the user
        $settings = Arag_Config::get('email_settings', NULL, 'core');
        $strings  = array ('appname'  => $appname,
                           'uri'      => html::anchor('multisite/frontend/index/' . $verify_uri),
                           'username' => $username,
                           'password' => $password);
        
        try {
            $is_sent = $this->MultiSite->sendEmail($email, $strings, $settings);
        } catch(Swift_Exception $e) {
            // Shit, there was an error here!
            $is_sent = False;
        }

        $this->session->set('multisite_installed', true);
        $this->session->set('multisite_email_is_sent', $is_sent);
        
        // Okay, everything is done so redirect to post installer
        url::redirect('multisite/backend/postinstaller');
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
