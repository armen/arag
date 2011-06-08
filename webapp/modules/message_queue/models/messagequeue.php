<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class MessageQueue_Model extends Model
{
    // {{{ Properties

    protected $client_storage;

    // }}}
    // {{{ Constants

    const COMMON_CHANNEL  = 'common';
    const DAILY_CHANNEL   = 'daily';
    const HOURLY_CHANNEL  = 'hourly';
    const MONTHLY_CHANNEL = 'monthly';
    const WEEKLY_CHANNEL  = 'weekly';

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        require_once Arag::find_file('message_queue', 'vendor', 'dropr/classes/dropr', True);

        $client_storage_type  = Kohana::config('dropr.client_storage_type');
        $client_storage_dsn   = Kohana::config('dropr.client_storage_dsn');
        $this->client_storage = dropr_Client_Storage_Abstract::factory($client_storage_type, $client_storage_dsn);
    }
    // }}}
    // {{{ queueMessage
    public function queueMessage($channel, $module, $model, $method, $arguments = Array())
    {
        $queue = new dropr_Client($this->client_storage);
        $peer  = dropr_Client_Peer_Abstract::getInstance('HttpUpload', url::site('message_queue/frontend/transporter'));

        $message['module']       = $module;
        $message['model']        = $model;
        $message['method']       = $method;
        $message['arguments']    = is_array($arguments) ? $arguments : Array($arguments);
        $message['created_date'] = time();
        $message['created_by']   = Session::instance()->get('user.username', Null);

        $input_message = serialize($message);
        $message       = $queue->createMessage($input_message, $peer, $channel);
        $message->queue();
    }
    // }}}
}
