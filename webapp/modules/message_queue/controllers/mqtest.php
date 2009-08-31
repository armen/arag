<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Mqtest_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        // Do not call parent constructor
    }
    // }}}
    // {{{ index_any
    public function index_any()
    {
        $count = 1;
        $message_queue = new MessageQueue_Model;
        $message_queue->queueMessage(MessageQueue_Model::COMMON_CHANNEL, 'message_queue', 'Mqtest', 'worker', Array('Test message', microtime()));

        if (rand(0, 1)) {
            $message_queue->queueMessage(MessageQueue_Model::HOURLY_CHANNEL, 'message_queue', 'Mqtest', 'worker', Array('Test message', microtime()));
            $count++;
        }

        die($count.' message(s) has been sent!');
    }
    // }}}
}
