<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthStore', True);
require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthRequestVerifier', True);

class OAuthApi extends Controller
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

                exit;

            } catch (OAuthException $e) {

                // The request was signed, but failed verification
                header('HTTP/1.1 401 Unauthorized');
                header('WWW-Authenticate: OAuth realm=""');
                header('Content-Type: text/plain; charset=utf8');

                echo $e->getMessage();
                exit;
            }
        }

        header('HTTP/1.1 403 Forbidden');
        header('WWW-Authenticate: OAuth realm=""');
        header('Content-Type: text/plain; charset=utf8');
        exit;
    }
    // }}}
    // {{{ index_any
    public function index_any()
    {
        echo 'Hello, World!';
    }
    // }}}
}
