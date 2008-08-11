<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.3
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Model Class
 *
 * @package     Arag
 * @subpackage  Libraries
 * @author      Armen Baghumian
 * @category    Token
 */
class Token {

    // {{{ generate
    public static function generate()
    {
        $session = Session::instance();
        $tokens  = (array) $session->get('arag_tokens', Array());

        // Generate token
        $token          = sha1(rand().time());
        $tokens[$token] = time();

        // Add token to tokens list
        $session->set('arag_tokens', $tokens);

        return $token;
    }
    // }}}
    // {{{ validate
    public static function validate()
    {
        $session = Session::instance();
        $input   = new Input();

        $tokens = $session->get('arag_tokens');
        $token  = $input->post('arag_token');

        if (!empty($token) && count($input->post()) && (!isset($tokens[$token]) || $tokens[$token] + Kohana::config('token.life_time') < time())) {
            // At this point we can't Kohana::config('locale.lang') because Arag_site_lang
            // is not executed so we use cookie to get the lang.
            $lang = (string) cookie::get('lang');
            $controller = new Controller;
            $controller->_invalid_request(Null, _("You tried to resubmit a form"));

        } else {
            unset($tokens[$token]);
            $session->set('arag_tokens', $tokens);
        }
    }
    // }}}
    // {{{ gc
    public static function gc()
    {
        /*  Infinity lifetime is not good idea for tokens
         *
         *  if (Kohana::config('token.life_time') == 0) {
         *      return;
         *  }
         */

        $session      = Session::instance();
        $tokens       = (array) $session->get('arag_tokens', Array());
        $alive_tokens = Array();

        foreach ($tokens as $token => $time) {

            if ($time + Kohana::config('token.life_time') > time()) {
                $alive_tokens[$token] = $time;
            }
        }

        $session->set('arag_tokens', $alive_tokens);
    }
    // }}}
}
