<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class MessageQueue_Frontend extends Controller
{
    // {{{ Properties

    protected $server_storage;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Dummy? but important :)
        // This disabled displaying site layout

        require Arag::find_file('message_queue', 'vendor', 'dropr/classes/dropr', True);

        // Make sure it's been ran from command line
        (PHP_SAPI != 'cli') AND die("Invalid Request!");

        $server_storage_type  = Kohana::config('dropr.server_storage_type');
        $server_storage_dsn   = Kohana::config('dropr.server_storage_dsn');
        $this->server_storage = dropr_Server_Storage_Abstract::factory($server_storage_type, $server_storage_dsn);
    }
    // }}}
}
