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
        $session     = Session::instance();
        $users       = Model::load('Users', 'user');
        $appname     = $session->get('user.appname', APPNAME);
        $username    = $session->get('user.username');
        $destination = implode('/', Router::$rsegments);
        $destination = rtrim($destination, '/').'/';      // Append a slash to $destination

        if (!$session->get('user.authenticated') && $username != 'anonymous') {

            // Fetch Anonymouse user information and store in session
            $session->set(Array('user' => $users->getAnonymouseUser($appname)));
        }

        if ($session->get('user.appname') != APPNAME) {

            // User switched between applications, fetch user information of selected
            // application and store it to session, as far as user will not switch between
            // applications often, so its ok to hit database every time.
            $user = $username ? $users->getUser($username, APPNAME) : Null;
            $user = isset($user['username']) ? $user : $users->getAnonymouseUser(APPNAME);

            // Throw away personal information, we already set those in session
            if ($username) {
                unset($user['name'], $user['username'], $user['lastname'], $user['email']);
            }

            // Merge user privileges of current application with existed privileges
            $user['privileges'] = array_merge($session->get('user.privileges', Array()), $user['privileges']);
            $session->set('user', array_merge($session->get('user', Array()), $user));
        }

        if (!self::is_accessible($destination, False)) {

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
    public static function is_accessible($uri, $routed_uri = True)
    {
        static $cache = Array();

        if ($routed_uri) {
            $uri = Router::routed_uri($uri);
        }

        if (!isset($cache[$uri])) {

            $session = Session::instance();
            $appname = $session->get('user.appname', APPNAME);
            $filters = $session->get('privilege_filters.'.$appname, False);

            // When user Logins privilege_filters unset by login method
            // then we fetch current application privilege filters here
            if ($filters == False) {

                // Fetch privilege filters for current application
                $filtersModel = Model::load('Filters', 'user');
                $session->set('privilege_filters', array_merge($session->get('privilege_filters', Array()), Array($appname => $filtersModel->getPrivilegeFilters($appname))));
            }

            $privileges = $session->get('user.privileges');
            $authorized = self::is_authorized($uri, $privileges[$appname]);

            if ($authorized) {
                // The user is authorized so we will try to filter his/her privileges with a blacklist
                $authorized = self::is_authorized($uri, $session->get('privilege_filters.'.$appname, Array()), False);
            }

            $cache[$uri] = $authorized;
        }

        return $cache[$uri];
    }
    // }}}
}
