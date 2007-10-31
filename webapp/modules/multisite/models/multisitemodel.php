<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class MultiSiteModel extends Model 
{
    // {{{ Properties
    
    var $tableName;

    // }}}
    // {{{ Constructor
    function MultiSiteModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // Set the table name
        $this->tableName = 'multisite_databases';
    }
    // }}}
}

?>
