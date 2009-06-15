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
        $this->namespace = Session::instance()->get("logger_appname", APPNAME);
    }
    // }}}
    // {{{ insertLog
    function insertLog($uri, $username, $date, $namespace = '')
    {
        $row = array( 'namespace'   => $namespace ? $namespace : $this->namespace,
                      'uri'         => $uri,
                      'owner'       => $username,
                      'date'        => $date,
                      'archive'     => 0 );
       $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ search
    function search($archive_status, $user_name, $operation, $datefrom, $dateto, $appname)
    {
        $this->db->select('id, namespace, uri, owner, date, archive');
        $this->db->Where('archive', $archive_status);
        $this->db->Where('namespace', $appname);

        if (isset($user_name) and $user_name) {
            $this->db->like('owner', $user_name);
        }

        if (isset($operation) and $operation) {
            $this->db->where('uri', $operation);
        }

        if ($datefrom && !$dateto) {
            $this->db->where('date >=', $datefrom);
            $this->db->where('date <=', $datefrom + 86400);
        }

        if ($datefrom && $dateto) {
            $this->db->where('date >=', $datefrom);
            $this->db->where('date <=', $dateto);
        }

        if (!$datefrom && $dateto) {
            $this->db->where('date <=', $dateto);
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
