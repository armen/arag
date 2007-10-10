<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Language Class
 *
 * @package     Arag
 * @subpackage  Libraries
 * @category    Language
 * @author      Armen Baghumian
 */
class Arag_Language {
    
    // {{{ Properties
    
    var $i18n_language;
    var $i18n_language_charset;
    var $i18n_gettext_domain;
    var $i18n_gettext_msgsdir;

    // }}}
    // {{{ load
    function load() 
    {
        $CI =& get_instance();

        // Load the session library
        $CI->load->library('session');

        $this->i18n_language         = $CI->config->item('Arag_i18n_language');
        $this->i18n_language_charset = $CI->config->item('Arag_i18n_language_charset');
        $this->i18n_gettext_domain   = $CI->config->item('Arag_i18n_gettext_domain');
        $this->i18n_gettext_msgsdir  = $CI->config->item('Arag_i18n_gettext_msgsdir');
        
        if ($language = $CI->session->userdata('language')) {
            $this->i18n_language = $language;
        } else {
            $CI->session->set_userdata(Array('language' => $this->i18n_language));
        }

        // Set environment variables
        putenv('LANG=' . $this->i18n_language);
        putenv('LANGUAGE=' . $this->i18n_language);
        setlocale(LC_ALL, $this->i18n_language);
        
        $module = APPPATH . 'modules/' . $CI->uri->router->fetch_module() . '/';

        bindtextdomain($this->i18n_gettext_domain,  $module . $this->i18n_gettext_msgsdir);
        bind_textdomain_codeset($this->i18n_gettext_domain, $this->i18n_language_charset);
        textdomain($this->i18n_gettext_domain);
    }
    // }}}
    // {{{ line
    function line($line)
    {
        return $this->translate($line);
    }
    // }}}
    // {{{ translate
    function translate($text) 
    {
        return gettext($text);
    }
    // }}}
}

?>
