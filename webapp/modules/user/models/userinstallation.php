<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class UserInstallation_Model extends Model
{
    // {{{ install
    public function install($installation)
    {
        // Execute all database schemas
        $installation->executeSchemas('user', '0.1');
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
