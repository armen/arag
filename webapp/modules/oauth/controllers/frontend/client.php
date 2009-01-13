<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthStore', True);
require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthRequester', True);

class Client_Controller extends Controller
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
    public function request_token_any($consumer_key)
    {
        // Fetch the id of the current user
        $user_id = 1;

        // Obtain a request token from the server
        $token = OAuthRequester::requestRequestToken($consumer_key, $user_id, array('oauth_consumer_key' => $consumer_key));

        // Callback url
        $callback_url = Kohana::config('oauth.callback_url').'?consumer_key='.$consumer_key.'&user_id='.$user_id;

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
    // {{{ callback_read
    public function callback_read()
    {
        // Request parameters are oauth_token, consumer_key and usr_id.
        $consumer_key = $_GET['consumer_key'];
        $oauth_token  = $_GET['oauth_token'];
        $user_id      = $_GET['user_id'];

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
            $error[] = $e->getMessage();
            $error[] = "1. Was already ok";
            $error[] = "2. We were not authorized";

            echo nl2br(implode("\n", $error));
        }
    }
    // }}}
    // {{{ do_request_any
    public function do_request_any()
    {
        $user_id     = 1;
        $request_uri = Kohana::config('oauth.api');
        $method      = Router::$method == 'read' ? 'GET' : 'POST';

        try
        {
            // Obtain a request object for the request we want to make
            $req = new OAuthRequester($request_uri, $method , $_REQUEST);

            // Sign the request, perform a curl request and return the results, throws OAuthException exception on an error
            $result = $req->doRequest($user_id);

            echo $result['body'];

        } catch (OAuthException $e) {
            echo $e->getMessage();
        }
    }
    // }}}
}
