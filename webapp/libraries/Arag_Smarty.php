<?php

include_once "smarty/Smarty.class.php";

class Arag_Smarty_Core extends Smarty {

    // {{{ Constructor
    function __construct()
    {
        // Check if we should use smarty or not
        if (Config::item('smarty.integration') == False) {
            return;
        }

        // Okay, integration is enabled, so call the parent constructor
        parent::Smarty();

        $this->template_dir   = MODPATH . Router::$module . '/views';
        $this->cache_dir      = Config::item('smarty.cache_path') . 'smarty_cache/';
        $this->compile_dir    = Config::item('smarty.cache_path') . 'smarty_compile/';
        $this->config_dir     = Config::item('smarty.global_templates_path') . 'smarty_configs/';
        $this->plugins_dir    = array_merge($this->plugins_dir, Config::item('smarty.plugins_paths'));
        $this->debug_tpl      = Config::item('smarty.global_templates_path') . 'arag_debug.tpl';
        $this->debugging_ctrl = Config::item('smarty.debugging_ctrl');
        $this->debugging      = Config::item('smarty.debugging');
        $this->caching        = Config::item('smarty.caching');
        $this->force_compile  = Config::item('smarty.force_compile');
        $this->security       = Config::item('smarty.security');
        $this->compile_id     = sha1(APPNAME.Config::item('locale.lang'));

        // check if cache directory is exists
        $this->checkDirectory(Config::item('arag.cache_path'));

        // check if smarty_compile directory is exists
        $this->checkDirectory($this->compile_dir);
        
        // check if smarty_cache directory is exists
        $this->checkDirectory($this->cache_dir);

        if ($this->security) {
            
            $configSecureDirectories = Config::item('smarty.secure_dirs');
            $safeTemplates           = Array(Config::item('smarty.global_templates_path'));

            $this->secure_dir                          = array_merge($configSecureDirectories, $safeTemplates);
            $this->security_settings['IF_FUNCS']       = Config::item('smarty.if_funcs');
            $this->security_settings['MODIFIER_FUNCS'] = Config::item('smarty.modifier_funcs');
        }    
        
        // Autoload filters
        $this->autoload_filters = Array('pre'    => Config::item('smarty.pre_filters'),
                                        'post'   => Config::item('smarty.post_filters'),
                                        'output' => Config::item('smarty.output_filters'));
    }
    // }}}
    // {{{ checkDirectory
    public function checkDirectory($directory)
    {
        if ((!file_exists($directory) && !@mkdir($directory, 0755)) ||
            !is_writeable($directory) || !is_executable($directory)) {

            $error = 'Compile/Cache directory "%s" is not writeable/executable';
            $error = sprintf($error, $directory);

            Kohana::show_error('Compile/Cache directory is not writeable/executable', $error);
        }
        
        return True;
    }
    // }}}
}

?>
