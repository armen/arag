<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthStore', True);
require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthServer', True);

class Server_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        $connection  = parse_url(Kohana::config('database.default.connection'));
        $this->store = OAuthStore::instance('MySQL', Array('server' => $connection['host'],
                                                           'password' => $connection['pass'],
                                                           'username' => $connection['user'],
                                                           'database' => ltrim($connection['path'], '/')));
    }
    // }}}
    // {{{ request_token_any
    public function request_token_any()
    {
        $server = new OAuthServer();
        $token  = $server->requestToken();
    }
    // }}}
    // {{{ authorize
    public function authorize_any()
    {
        // The current user
        $user_id = 1;

        // Fetch the oauth store and the oauth server.
        $store  = OAuthStore::instance();
        $server = new OAuthServer();

        try {

            $rs = $server->authorizeVerify();
            $server->authorizeFinish(True, $user_id);

        } catch (OAuthException $e) {

            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: text/plain');

            echo "Failed OAuth Request: " . $e->getMessage();
        }
    }
    // }}}
    // {{{ access_token_any
    public function access_token_any()
    {
        $server = new OAuthServer();
        $server->accessToken();
    }
    // }}}
}
