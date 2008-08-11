<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class User_ProfileInstallation_Model extends Model
{
    // {{{ install
    public function install($installation)
    {
        // Execute all database schemas
        $installation->executeSchemas('user_profile', '0.1');
    }
    // }}}
    // {{{ upgrade
    public function upgrade($installation, $version)
    {
    }
    // }}}
    // {{{ uninstall
    public function uninstall($installation)
    {
    }
    // }}}
}

?>
