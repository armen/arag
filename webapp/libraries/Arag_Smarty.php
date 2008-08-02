<?php

include_once "smarty/Smarty.class.php";

class Arag_Smarty_Core extends Smarty {

    // {{{ Constructor
    public function __construct()
    {
        // Check if we should use smarty or not
        if (Kohana::config('smarty.integration') == False) {
            return;
        }

        // Okay, integration is enabled, so call the parent constructor
        parent::Smarty();

        $this->template_dir   = MODPATH . Router::$module . '/views';
        $this->cache_dir      = Kohana::config('smarty.cache_path') . 'smarty_cache/';
        $this->compile_dir    = Kohana::config('smarty.cache_path') . 'smarty_compile/';
        $this->config_dir     = Kohana::config('smarty.global_templates_path') . 'smarty_configs/';
        $this->plugins_dir    = array_merge($this->plugins_dir, Kohana::config('smarty.plugins_paths'));
        $this->debug_tpl      = Kohana::config('smarty.global_templates_path') . 'arag_debug.tpl';
        $this->debugging_ctrl = Kohana::config('smarty.debugging_ctrl');
        $this->debugging      = Kohana::config('smarty.debugging');
        $this->caching        = Kohana::config('smarty.caching');
        $this->force_compile  = Kohana::config('smarty.force_compile');
        $this->security       = Kohana::config('smarty.security');
        $this->compile_id     = sha1(APPNAME.Kohana::config('locale.lang'));

        // check if cache directory is exists
        $this->checkDirectory(Kohana::config('arag.cache_path'));

        // check if smarty_compile directory is exists
        $this->checkDirectory($this->compile_dir);

        // check if smarty_cache directory is exists
        $this->checkDirectory($this->cache_dir);

        if ($this->security) {

            $configSecureDirectories = Kohana::config('smarty.secure_dirs');
            $safeTemplates           = Array(Kohana::config('smarty.global_templates_path'));

            $this->secure_dir                          = array_merge($configSecureDirectories, $safeTemplates);
            $this->security_settings['IF_FUNCS']       = Kohana::config('smarty.if_funcs');
            $this->security_settings['MODIFIER_FUNCS'] = Kohana::config('smarty.modifier_funcs');
        }

        // Autoload filters
        $this->autoload_filters = Array('pre'    => Kohana::config('smarty.pre_filters'),
                                        'post'   => Kohana::config('smarty.post_filters'),
                                        'output' => Kohana::config('smarty.output_filters'));
    }
    // }}}
    // {{{ checkDirectory
    public function checkDirectory($directory)
    {
        if ((!file_exists($directory) && !@mkdir($directory, 0755)) ||
            !is_writeable($directory) || !is_executable($directory)) {

            $error = 'Compile/Cache directory "%s" is not writeable/executable';
            $error = sprintf($error, $directory);

            throw new Kohana_User_Exception('Compile/Cache directory is not writeable/executable', $error);
        }

        return True;
    }
    // }}}
}
