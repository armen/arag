<?php

if (Config::item('token.enable')) {
    Event::add('system.ready', 'arag_token');
}

function arag_token()
{
    if (Config::item('token.type') === 'form') {

        $session = Session::instance();
        $input   = new Input();
    
        $old_token  = $session->get_once('arag_token');
        $user_token = $input->post('arag_token');

        if (!empty($old_token) && count($input->post()) && $old_token !== $user_token) {
            // At this point we can't Config::item('locale.lang') because Arag_site_lang 
            // is not executed so we use cookie to get the lang.
            $lang = (string) cookie::get('lang');
            $controller = new Controller;
            $controller->_invalid_request(Null, _("You tried to resubmit a form"));
        }

        $session->set('arag_token', sha1(rand().time()));
    }
}

?>
