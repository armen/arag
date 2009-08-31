<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Transporter_Controller extends Controller
{
    // {{{ Properties

    protected $server_storage;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        // Dummy? but important :)
        // This disabled displaying site layout

        require Arag::find_file('message_queue', 'vendor', 'dropr/classes/dropr', True);

        $server_storage_type  = Kohana::config('dropr.server_storage_type');
        $server_storage_dsn   = Kohana::config('dropr.server_storage_dsn');
        $this->server_storage = dropr_Server_Storage_Abstract::factory($server_storage_type, $server_storage_dsn);
    }
    // }}}
    // {{{ index_any
    public function index_any()
    {
        $server = dropr_Server_Transport_Abstract::factory('HttpUpload', $this->server_storage);
        $server->handle();
    }
    // }}}
}
