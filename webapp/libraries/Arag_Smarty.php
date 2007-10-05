<?php if (!defined('BASEPATH')) exit('No direct script access allowed');  

include_once "smarty/Smarty.class.php";

class Arag_Smarty extends Smarty {

    // {{{ Constructor
    function Arag_Smarty() 
    {
        global $RTR;
        $CI =& get_instance();

        // Check if we should use smarty or not
        if ($CI->config->item('Arag_smarty_integriation') == False) {
            return;
        }

        // Okey, integriation is enabled, so call the parent constructor
        parent::Smarty();

        // Make smarty accessable in controller
        $CI->smarty =& $this;

        $this->template_dir   = $CI->config->item('Arag_templates_path');
        $this->cache_dir      = $CI->config->item('Arag_cache_path') . 'smarty_cache/';
        $this->compile_dir    = $CI->config->item('Arag_cache_path') . 'smarty_compile/';
        $this->config_dir     = $CI->config->item('Arag_templates_path') . 'smarty_configs/';
        $this->plugins_dir[]  = $CI->config->item('Arag_templates_path') . 'smarty_plugins/';
        $this->debug_tpl      = $CI->config->item('Arag_templates_path') . 'arag_debug.tpl';
        $this->debugging_ctrl = $CI->config->item('Arag_debugging_ctrl');
        $this->debugging      = $CI->config->item('Arag_smarty_debugging');
        $this->caching        = $CI->config->item('Arag_smarty_caching');
        $this->force_compile  = $CI->config->item('Arag_smarty_force_compile');
        $this->security       = $CI->config->item('Arag_smarty_security');

        // check if compile directory is exists
        $this->checkDirectory($this->compile_dir);
        
        // check if cache directory is exists
        $this->checkDirectory($this->cache_dir);

        if ($this->security) {
            
            $configSecureDirectories = $CI->config->item('Arag_smarty_secure_dirs');
            $modulesView             = Array(APPPATH . 'modules/'. $RTR->fetch_module() . '/templates');
            //$modulesView            = $this->getModulesViewPath();

            $this->secure_dir                          = array_merge($configSecureDirectories, $modulesView);
            $this->security_settings['IF_FUNCS']       = $CI->config->item('Arag_smarty_if_funcs');
            $this->security_settings['MODIFIER_FUNCS'] = $CI->config->item('Arag_smarty_modifier_funcs');
        }    
        
        // Autoload filters
        $this->autoload_filters = Array('pre'    => $CI->config->item('Arag_smarty_pre_filters'),
                                        'post'   => $CI->config->item('Arag_smarty_post_filters'),
                                        'output' => $CI->config->item('Arag_smarty_output_filters'));
    
        // Send base_url to all templates
        $this->assign('arag_base_url', $CI->config->item('base_url'));
    }
    // }}}
    // {{{ checkDirectory
    function checkDirectory($directory)
    {
        if ((!file_exists($directory) && !@mkdir($directory, 0755)) ||
            !is_writeable($directory) || !is_executable($directory)) {

            $error = 'Compile/Cache directory "%s" is not writeable/executable';
            $error = sprintf($error, $directory);

            show_error($error);
        }
        
        return True;
    }
    // }}}
    // {{{ & getModulesViewPath
    function & getModulesViewPath()
    {
        $modules = Array();        

        if ($dh = opendir(APPPATH.'modules')) {
    
            while (false !== ($moduleName = readdir($dh))) {

                if ($moduleName != '.' && $moduleName != '..' && 
                    $moduleName != 'CVS' && $moduleName != '.svn') {

                    $modules[] = APPPATH . 'modules/'. $moduleName . '/templates';
                }
            }
        
            closedir($dh);
        }

        return $modules;
    }
    // }}}
}

?>
