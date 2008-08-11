<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class MultiSiteInstallation_Model extends Model
{
    // {{{ install
    public function install($installation)
    {
        // Execute all database schemas
        $installation->executeSchemas('multisite', '0.1');
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
