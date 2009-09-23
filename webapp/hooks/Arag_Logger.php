<?php
// Add the Arag_Logger::log at the begining
Event::add_before('system.execute', current(Event::get('system.execute')), array('Arag_Logger', 'log'));

class Arag_Logger {

    // {{{ log
    public static function log()
    {
        $current_uri = implode('/', Router::$rsegments);
        $uri         = Router::request_method().":".$current_uri;
        $messages    = Arag_Config::get('logger.messages', Null, 'logger', True);

        if (array_key_exists($uri, $messages)) {
            $username  = Session::instance()->get('user.username');
            $date      = time();
            $model     = Model::load('logger','logger');
            $model->insertLog($uri, $username, $date);
        }

    }
    // }}}
}
