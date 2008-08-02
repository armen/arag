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
    // {{{ Construct
    public function __construct()
    {
        parent::__construct();

        // Validation Messages
        $this->validation->message('required', _("%s is required"));
        $this->validation->message('matches', _("%ss do not match"));
        $this->validation->message('alpha_dash', _("%s can contain only alpha-numeric characters, underscores or dashes"));
        $this->validation->message('email', _("Please enter a valid email address"));
        $this->validation->message('_check_app', _("This application name is not available"));
    }
    // }}}
    // {{{ index_read
    public function index_read()
    {
        $multiSite = new MultiSite_Model;

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

        $data = array('modules'   => $multiSite->getModules($excludeModules),
                      'messages'  => $messages,
                      'show_form' => $show_form);

        $this->layout->content = new View('backend/install', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        // Load Models 
        $multiSite        = new MultiSite_Model;
        $coreInstallation = Model::load('Installation', 'core');
        $groups           = Model::load('Groups', 'user');
        $applications     = Model::load('Applications', 'user');
        $users            = Model::load('Users', 'user');

        $appname    = $this->input->post('appname', Null, true);
        $email      = $this->input->post('email', Null, true);
        $modules    = $this->input->post('modules', Null, true);
        $author     = $this->session->get('user.username');
        $anonypri   = serialize(Arag_Config::get('anonypri', NULL));
        $adminpri   = serialize(Arag_Config::get('adminpri', NULL));
        $verify_uri = $multiSite->generateVerifyUri(10);
        $password   = strtolower(text::random('alnum', Arag_Config::get('passlength', 8, 'user')));
        $username   = $appname.'_admin';

        // Get next database data source name and id
        $multiSite->getNextDB($DSN, $databaseID);

        // Set the DSN and tablePrefix
        $tablePrefix = str_replace('.', '_', $appname) . '_';
        $coreInstallation->setTablePrefix($tablePrefix);
        $coreInstallation->setDSN($DSN);        
        
        $applications->addApp($appname, $author, 'anonymous', $databaseID);      // Create the application
        $groups->newGroup($appname, 'admin', $author, $adminpri);         // Create admin group of this application
        $groups->newGroup($appname, 'anonymous', $author, $anonypri);     // Create anonymous group of this application
        $users->createUser($appname, $email, NULL, NULL, 'admin', $username, $password, $author, $verify_uri, 0);

        $base_tpl_path = MODPATH . 'multisite/templates';

        // Create symlink in to the /var/www, this is just for local testing
        @symlink(DOCROOT . 'sites/'.$appname, '/var/www/'.$appname);

        // Create directory of application
        $coreInstallation->createDirectory(DOCROOT . 'sites/'.$appname);

        // Create scripts symbolic link
        symlink(DOCROOT . '/scripts', DOCROOT . 'sites/'.$appname.'/scripts');

        // Create index file
        $coreInstallation->createFromTemplate(DOCROOT . 'sites/'.$appname.'/index.php', 'index.php', Array(), $base_tpl_path);

        // Create config file
        $parameters = Array('dsn' => $DSN, 'table_prefix' => $tablePrefix, 'parent_base_url' => url::base());
        $coreInstallation->createFromTemplate(APPPATH . 'config/sites/'.$appname.'.php', 'config.php', $parameters, $base_tpl_path);

        // Core module is required
        if (empty($modules) || !is_array($modules) || !in_array('core', $modules)) {
            $modules[] = 'core';
        }
        
        foreach ($modules as $module) {
            
            $Installation = ucfirst($module) . 'Installation';
            $Installation = Model::load($Installation, $module);
            
            if (is_callable(array($Installation, 'install'))) {
                
                // Change include_once to module path
                Kohana::config_set('core.modules', Array(MODPATH.$module));
                
                $Installation->install($coreInstallation);

                // Reset the include_paths
                Kohana::config_set('core.modules', Array(MODPATH.Router::$module));
            }
        }

        // Send an email to verify the user
        $settings = Arag_Config::get('email_settings', NULL, 'core');
        $strings  = array ('appname'  => $appname,
                           'uri'      => html::anchor('multisite/frontend/index/' . $verify_uri),
                           'username' => $username,
                           'password' => $password);
        
        try {
            $is_sent = $multiSite->sendEmail($email, $strings, $settings);
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
    // {{{ index_validate_write
    public function index_validate_write()
    {
        $this->validation->name('appname', _("Application name"))->pre_filter('trim', 'appname')
             ->add_rules('appname', 'required', 'valid::alpha_dash', array($this, '_check_app'));

        $this->validation->name('email', _("Email"))->pre_filter('trim', 'email')
             ->add_rules('email', 'required', 'matches[reemail]', 'valid::email');

        $this->validation->name('reemail', _("Retype Email"))->pre_filter('trim', 'reemail')
             ->add_rules('reemail', 'required');

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
