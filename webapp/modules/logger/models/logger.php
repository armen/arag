<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani <jilakhaghani@gmail.com>                          |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------
class Logger_Model extends Model
{
    // {{{ __construct
    function __construct()
    {
        parent::__construct();
        $this->tableName = 'logger';
    }
    // }}}
    // {{{ insertLog
    function insertLog($uri, $username, $date)
    {
        $row = array( 'uri'    => $uri,
                      'owner'  => $username,
                      'date'   => $date,
                      'archive'=> 0 );
       $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ search
    function search($archive_status, $user_name, $operation, $date)
    {
        $this->db->select('id, uri, owner, date, archive');
        $this->db->Where('archive', $archive_status);

        if ($user_name) {
            $this->db->like('owner', $user_name);
        }

        if ($operation) {
            $this->db->where('uri', $operation);
        }

        if ($date) {
            $this->db->where('date >=', $date);
            $this->db->where('date <=', $date + 86400);
        }

        $query = $this->db->get($this->tableName);
        $result = $query->result_array(false);

        return $result;
    }
    // }}}
    // {{{ archive
    function archive($ids)
    {
        $row = array( 'archive' => 1);
        foreach ($ids as $id){
            $this->db->where('id', $id);
            $this->db->update($this->tableName, $row);
        }
    }
    // }}}
}