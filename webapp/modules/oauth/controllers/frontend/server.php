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
        $this->store = OAuthStore::instance('MySQL', Array('server' => $connection['host'], 'password' => $connection['pass'],
                                                           'username' => $connection['user'], 'database' => ltrim($connection['path'], '/')));
    }
    // }}}
    // {{{ __call
    public function __call($method, $args)
    {
        if (OAuthRequestVerifier::requestIsSigned()) {

            try {

                $req     = new OAuthRequestVerifier();
                $user_id = $req->verify();

                // If we have an user_id, then login as that user (for this request)
                if ($user_id) {
                    parent::__call($method, $args);
                }

            } catch (OAuthException $e) {

                // The request was signed, but failed verification
                header('HTTP/1.1 401 Unauthorized');
                header('WWW-Authenticate: OAuth realm=""');
                header('Content-Type: text/plain; charset=utf8');

                echo $e->getMessage();
                exit();
            }
        } else {
            parent::__call($method, $args);
        }
    }
    // }}}
    // {{{ request_request_token_any
    public function request_request_token_any($consumer_key)
    {
        require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthRequester', True);

        // Fetch the id of the current user
        $user_id = 1;

        // Obtain a request token from the server
        $token = OAuthRequester::requestRequestToken($consumer_key, $user_id, array('oauth_consumer_key' => $consumer_key));

        // Callback url
        $callback_url = Kohana::config('oauth.callback_url');

        // Now redirect to the autorization uri and get us authorized
        if (!empty($token['authorize_uri'])) {

            $uri  = strpos($token['authorize_uri'], '?') ? $token['authorize_uri'].'&' : $token['authorize_uri'].'?';
            $uri .= 'oauth_token='.rawurlencode($token['token']).'&oauth_callback='.rawurlencode($callback_url);

        } else {
            // No authorization uri, assume we are authorized, exchange request token for access token
            $uri = $callback_url;
        }

        header('Location: '.$uri);
        exit();
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
    // {{{ callback_any
    public function callback_any()
    {
        try
        {
            OAuthRequester::requestAccessToken($consumer_key, $oauth_token, $user_id);
        }
        catch (OAuthException $e)
        {
            // Something wrong with the oauth_token.
            // Could be:
            // 1. Was already ok
            // 2. We were not authorized
        }
    }
    // }}}
    // {{{ api_any
    public function api_any()
    {
        echo 'Hello, World!';
    }
    // }}}
}
