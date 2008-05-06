<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class ReportGenerator_Model extends Model 
{
    // {{{ Properties

    private $config = array();

    // }}}
    // {{{ constructor
    public function __construct()
    {
        $this->config           = defined('MASTERAPP') ? Config::item('database.default') : Config::item('sites/'.APPNAME.'.database');
        $this->config['object'] = False;
        
        parent::__construct(new Database($this->config));
    }
    // }}}
    // {{{ describe
    public function describe($table)
    {
        return $this->db->query("Describe ".$this->config['table_prefix'].$table)->result_array();
    }
    // }}}
}
