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
 * Loader Class
 *
 * Loads views and files
 *
 * @package     Arag
 * @subpackage  Hooks
 * @author      Armen Baghumian
 * @category    Auth
 */
class Arag_Auth {
    
    // {{{ Properties

    var $destination;

    // }}}
    // {{{ Constructor
    function Arag_Auth()
    {
        global $RTR;
        $this->destination = $RTR->fetch_module() . '/' . $RTR->fetch_directory() . $RTR->fetch_class() . '/' . $RTR->fetch_method();
    }    
    // }}}
    // {{{ check
    function check()
    {
        $CI =& get_instance();

        // Fetch current appname
        $appname = '_master_';

        if (!$CI->session->userdata('authenticated') && $CI->session->userdata('username') != 'anonymous') {
            // Fetch Anonymouse user information and store in session
            $CI->load->model(Array('UsersModel', 'user'), 'Users');        
            $CI->session->set_userdata($CI->Users->getAnonymouseUser($appname));
        }

        // When user Logins session destroied by login and we fetch current application
        // privilege filters here
        if ($CI->session->userdata('privilege_filters') === False) {
            // Fetch privilege filters for current application
            $CI->load->model(Array('FiltersModel', 'user'), 'Filters');
            $CI->session->set_userdata('privilege_filters', $CI->Filters->getPrivilegeFilters($appname));
        }

        $authorized = $this->isAuthorized($CI->session->userdata('privileges'));

        if ($authorized) {
            // The user is authorized so we will try to filter his/her privileges with a blacklist
            $authorized = $this->isAuthorized($CI->session->userdata('privilege_filters'), False);
        }

        if (!$authorized) {
            $CFG =& load_class('Config');
            header("location: " . $CFG->site_url('not_authorized'));
            exit;
        }
    }
    // }}}
    // {{{ isAuthorized
    function isAuthorized($privileges, $whiteList = True)
    {
        // XXX: We have to allow this destination otherwise it is possible to happen an infinity loop.
        if ($this->destination === 'core/frontend/messages/not_authorized') {
            return True;
        }
    
        if (is_array($privileges)) {

            // Privilege validation pattern
            $pattern = ((boolean) $whiteList) ? 
                       // It contains four section which every section separated with a /.
                       // It should contain at least one section. Each section contains * 
                       // (except first section) or lower case characters
                       '/^([a-z_]*)(\/([a-z_]*|\*)){0,3}$/' : 
                       // It contains four section which every section separated with a /.
                       // It should contain at least one section. Each section contains * 
                       // or lower case characters                        
                       '/^([a-z_]*|\*)(\/([a-z_]*|\*)){0,3}$/';

            foreach ($privileges as $privilege) {

                if (preg_match($pattern, $privilege)) {
                    
                    $privilege = str_replace('*', '.*', $privilege);
                    $privilege = '|^'.$privilege.'$|';

                    if (preg_match($privilege, $this->destination)) {
                        return (boolean) $whiteList;
                    }
                }
            }
        }

        return !(boolean) $whiteList;
    }
    // }}}
}

?>
