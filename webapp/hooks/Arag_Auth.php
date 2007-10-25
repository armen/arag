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

        if (!$CI->session->userdata('authenticated') && $CI->session->userdata('username') != 'anonymous') {
            $CI->load->model(Array('UsersModel', 'user'), 'Users');        
            $CI->session->set_userdata($CI->Users->getAnonymouseUser('_master_'));
        }

        $privileges = $CI->session->userdata('privileges');

        // XXX: We have to allow this destination otherwise it is possible to happen an infinity loop.
        //      Another reason of this line is converting a Null privileges to an Array
        $privileges[] = 'core/frontend/messages/not_authorized';

        $authorized = False;

        foreach ($privileges as $privilege) {

            // Validate the privilege, it contains four section which every section separated with a /.
            // it should contain at least one section. each section contains * (except first section) 
            // or lower case characters
            if (preg_match('/^([a-z_]*)(\/([a-z_]*|\*)){0,3}$/', $privilege)) {
                
                $privilege = str_replace('*', '.*', $privilege);
                $privilege = '|^'.$privilege.'$|';

                if (preg_match($privilege, $this->destination)) {
                    $authorized = True;
                    break;
                }
            }
        }

        if (!$authorized) {
            $CFG =& load_class('Config');
            header("location: " . $CFG->site_url('not_authorized'));
            exit;
        }
    }
    // }}}
}

?>
