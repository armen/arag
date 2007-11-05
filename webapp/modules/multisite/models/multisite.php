<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class MultiSite_Model extends Model 
{
    // {{{ Properties
    
    var $tableName;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Connecting to the database
// $this->load->database();

        // Set the table name
        $this->tableName = 'multisite_databases';
    }
    // }}}
}

?>
