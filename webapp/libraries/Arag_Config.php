<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 *               Sasan Rose  
 * @since        Version 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Loader Class
 *
 * Loads views and files
 *
 * @package     Arag
 * @subpackage  Libraries
 * @author      Armen Baghumian
 * @category    Loader
 */
class Arag_Config extends CI_Config {
    
    // {{{ load
    /**
     * Load Config File
     *
     * @access    public
     * @param    string    the config file name
     * @return    boolean    if the file was loaded correctly
     */    
    function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
    {
        $file = ($file == '') ? 'config' : str_replace(EXT, '', $file);

        if (in_array($file, $this->is_loaded, TRUE)) {
            return TRUE;
        }        
    
        if (preg_match('!^((/)|(\\\\))|([a-zA-Z]:((/)|(\\\\)))!', $file)) {
            $path = dirname($file) . '/';
            $file = basename($file);
        } else {
            $path = APPPATH.'config/';
        }

        if (!file_exists($path.$file.EXT)) {

            if ($fail_gracefully === TRUE) {
                return FALSE;
            }
            show_error('The configuration file '.$file.EXT.' does not exist.');
        }
    
        include($path.$file.EXT);

        if ( ! isset($config) OR ! is_array($config)) {

            if ($fail_gracefully === TRUE) {
                return FALSE;
            }        
            show_error('Your '.$file.EXT.' file does not appear to contain a valid configuration array.');
        }
        
        if ($use_sections === TRUE) {
            if (isset($this->config[$file])) {
                $this->config[$file] = array_merge($this->config[$file], $config);
            } else {
                $this->config[$file] = $config;
            }
        } else {
            $this->config = array_merge($this->config, $config);
        }

        $this->is_loaded[] = $file;
        unset($config);

        log_message('debug', 'Config file loaded: config/'.$file.EXT);
        return TRUE;
    }
    // }}}
    // {{{ item
    function item($item, $index = NULL, $defaultValue = Null, $namespace = Null)
    {

        if ($index == NULL) {
            $index = '';
        }

        $pref = parent::item($item, $index);

        if ($pref === FALSE) {
            $pref = $this->_fetch_item($item, $defaultValue, $namespace);
        }
        
        return $pref;

    }
    // }}}
    // {{{ fetch_item
    function _fetch_item($item, $defaultValue, $namespace)
    {
        $result = $defaultValue;
        
        global $CI;
            
        if (isset($CI)) {
            $CI->load->model('configuration');
            $result = $CI->configuration->getVar($item);
        }

        return $result;       
    }
    // }}}
    // {{{ set_item
    function set_item($item, $value, $namespace = NULL) {

        $pref = parent::item($item);

        if ($pref === FALSE) {
            
            global $CI;
            
            if (isset($CI)) {
                $CI->load->model('configuration');
                $result = $CI->configuration->setVar($item, $value, $namespace);
            }

        } else {
            parent::set_item($item, $value);
        }
  
    }
    // }}}
}

?>
