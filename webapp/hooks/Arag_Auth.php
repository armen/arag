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
        // Hard coded permissions :)
        $permissions = Array('', 'core/*', 'staticpages/*', 'ta_locator/*', 'ta_minor_profile/*', 'user/*', 'blog/*');
        
        // XXX: We have to allow this destinatin otherwise it is possible to happen an infinity loop 
        $permissions[] = 'core/frontend/messages/not_authorized';

        $authorized  = False;

        foreach ($permissions as $permission) {

            // Validate the permission, it contains four section which every section separated with a /.
            // it should contain at least one section. each section contains * (except first section) 
            // or lower case characters
            if (preg_match('/^([a-z_]*)(\/([a-z_]*|\*)){0,3}$/', $permission)) {
                
                $permission = str_replace('*', '.*', $permission);
                $permission = '|^'.$permission.'$|';

                if (preg_match($permission, $this->destination)) {
                    $authorized = True;
                    break;
                }
            }
        }

        if (!$authorized) {
            $CFG =& load_class('Config');
            header("location: " . $CFG->site_url('core/not_authorized'));
            exit;
        }
    }
    // }}}
}

?>
