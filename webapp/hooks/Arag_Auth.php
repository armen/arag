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
        // $permissions = Array('*', 'blog', 'blog/*', '*/backend/*', 'blog/backend/*', 'blog/*/index', 'blog/*/*', 'blog/backend/index');
        // $permissions = Array('blog/frontend/*', 'blog/backend/entry/post');
        $permissions = Array('*');
        $authorized  = False;

        foreach ($permissions as $permission) {

            // Validate the permission, it contains four section which every section separated with a /.
            // it should contain at least one section. each section contains * or lower case characters
            if (preg_match('/^([a-z]*|\*)(\/([a-z]*|\*)){0,3}$/', $permission)) {
                
                $permission = str_replace('*', '.*', $permission);
                $permission = '|^'.$permission.'$|';

                if (preg_match($permission, $this->destination)) {
                    $authorized = True;
                    break;
                }
            }
        }

        if (!$authorized) {
            die('you are not authorized');
        }
    }
    // }}}
}

?>
