<?php defined('SYSPATH') or die('No direct script access.');

Event::add('system.execute', array('Arag_Auth', 'check'));

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

    private static $destination;

    // }}}
    // {{{ check
    public static function check()
    {
        $session = new Session();
        $appname = $session->get('appname');

        if (APPNAME !== $appname && $session->get('authenticated') && $appname != '_master_') {
            
            // Redirect to user's application
            $multisite = Model::load('MultiSite', 'multisite');
            url::redirect($multisite->getAppUrl($appname));
            exit;
        }

        $directory         = substr(Router::$directory, strpos(Router::$directory, 'controllers/') + 12); // 12 is strlen('controllers/')
        self::$destination = Router::$module . '/' . $directory . Router::$controller . '/' . Router::$method;

        if (!$session->get('authenticated') && $session->get('username') != 'anonymous') {

            // Fetch Anonymouse user information and store in session
            $users = Model::load('Users', 'user');            
            $session->set($users->getAnonymouseUser($appname));
        }

        // When user Logins privilege_filters unset by login method 
        // then we fetch current application privilege filters here
        if ($session->get('privilege_filters') === False) {

            // Fetch privilege filters for current application
            $filters = Model::load('Filters', 'user');                        
            $session->set('privilege_filters', $filters->getPrivilegeFilters($appname));
        }

        $authorized = self::is_authorized($session->get('privileges'));

        if ($authorized) {
            // The user is authorized so we will try to filter his/her privileges with a blacklist
            $authorized = self::is_authorized($session->get('privilege_filters'), False);
        }

        if (!$authorized) {
            url::redirect('not_authorized');        
            exit;
        }
    }
    // }}}
    // {{{ is_authorized
    private static function is_authorized($privileges, $whiteList = True)
    {
        // XXX: We have to allow this destination otherwise it is possible to happen an infinity loop.
        if (self::$destination === 'core/frontend/messages/not_authorized') {
            return True;
        }

        if (is_array($privileges)) {

            foreach ($privileges as $privilege) {

                // BLAKLIST: 
                //     * is allowed when we are working with black lists
                if (((boolean) $whiteList == False && $privilege === '*') || 
                    // BLAKLIST:
                    //     It contains four section which every section separated with a /.
                    //     It should contain at least two sections. last section allways is *
                    //     and othe sections are lower case cheractrer(s)
                    ((boolean) $whiteList == False && preg_match('/^([a-z_]+)((\/[a-z_]+){0,2}(\/\*))$/', $privilege)) ||
                    // WHITELIST:
                    //     It contains four section which every section separated with a /.
                    //     It should contain at least two sections. Each section contains * 
                    //     (except first section and last when we have 4 or 3 sections) or 
                    //     lower case character(s)
                    ((boolean) $whiteList == True && preg_match('/^([a-z_]+)((\/[a-z_]+){0,2}(\/\*))|((\/[a-z_]+){2,3})$/', $privilege))) {
                    
                    // Replace * with .*
                    $privilege = '|^'.str_replace('*', '.*', $privilege).'$|';

                    if (preg_match($privilege, self::$destination)) {
                        return (boolean) $whiteList;
                    }

                } else if ((boolean) $whiteList == False) {
                    // We are checking filters which are blacklist and the filter is invalid so 
                    // return False
                    return False;

                } // else, privilege is invalid, ignore it
            }
            
            // Return depend on list type
            return !(boolean) $whiteList;
        }

        // The format is incorrect! sorry we can't let you in :)
        return False;
    }
    // }}}
}

?>
