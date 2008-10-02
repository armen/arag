<?php

// Add the Arag_Auth::check at the begining
Event::add_before('system.execute', current(Event::get('system.execute')), array('Arag_Auth', 'check'));

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

    // {{{ check
    public static function check()
    {
        $session = Session::instance();
        $appname = $session->get('user.appname', APPNAME);

        $destination = implode('/', Router::$rsegments);
        $destination = rtrim($destination, '/').'/';      // Append a slash to $destination

        if (!$session->get('user.authenticated') && $session->get('user.username') != 'anonymous') {

            // Fetch Anonymouse user information and store in session
            $users = Model::load('Users', 'user');
            $session->set(Array('user' => $users->getAnonymouseUser($appname)));
        }

        if (!self::is_accessible($destination, True)) {
            if (!$session->get('user.authenticated')) {
                $session->set_flash('not_authorized_redirect_url', $destination);
            }
            url::redirect('not_authorized');
            exit;
        }
    }
    // }}}
    // {{{ is_authorized
    private static function is_authorized($destination, $privileges, $whiteList = True)
    {
        // Append a slash to $destination
        $destination = rtrim($destination, '/').'/';

        // XXX: We have to allow this destination otherwise it is possible to happen an infinity loop.
        if ($destination === 'core/frontend/messages/not_authorized/') {
            return True;
        }

        if (is_array($privileges)) {

            foreach ($privileges as $privilege) {

                // BLAKLIST:
                //     * is allowed when we are working with black lists
                if (((boolean) $whiteList == False && $privilege === '*') ||
                    // BLAKLIST:
                    //     It contains four sections which every section separated with a /.
                    //     It should contain at least two sections. last section allways is *
                    //     and other sections are lower case cheractrer(s)
                    ((boolean) $whiteList == False && preg_match('/^[a-z_]+(\/[a-z_]+){0,2}\/\*$/', $privilege)) ||
                    // WHITELIST:
                    //     Which can include urls and rules. URL contains four section which every section
                    //     separated with a /. It should contain at least two
                    //     sections. Each section can contain *
                    //     (except first and last section when we have 4 or 3 sections) or
                    //     lower case character(s). At the other hand, a rule
                    //     should be initialized with a @ at the beggining and
                    //     contain two sections. in which the second one can
                    //     contain * too.
                    ((boolean) $whiteList == True &&
                     preg_match('/^(([a-z_]+(((\/[a-z_]+){0,2}\/\*)|((\/[a-z_]+){2,3})))|(@[a-z_]+\/(([a-z_]+)|(\*))))$/', $privilege))) {

                    // Replace * with .*
                    $privilege = '|^'.str_replace('*', '.*', $privilege).'$|';

                    if (preg_match($privilege, $destination)) {
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
    // {{{ is_accessible
    public static function is_accessible($uri, $routed_uri = False)
    {
        $session = Session::instance();
        $appname = $session->get('user.appname', APPNAME);

        // When user Logins privilege_filters unset by login method
        // then we fetch current application privilege filters here
        if ($session->get('privilege_filters') === False) {

            // Fetch privilege filters for current application
            $filters = Model::load('Filters', 'user');
            $session->set('privilege_filters', $filters->getPrivilegeFilters($appname));
        }

        if (!$routed_uri) {
            $uri = Router::routed_uri($uri);
        }

        $authorized = self::is_authorized($uri, $session->get('user.privileges'));

        if ($authorized) {
            // The user is authorized so we will try to filter his/her privileges with a blacklist
            $authorized = self::is_authorized($uri, $session->get('privilege_filters'), False);
        }

        return $authorized;
    }
    // }}}
}
