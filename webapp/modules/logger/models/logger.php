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
    function search($params)
    {
        $this->db->select('id, namespace, uri, owner, date, archive');
        $this->db->Where('archive', $params['archive_status']);

        if (isset($params['user_name']) and $params['user_name']) {
            $this->db->like('owner', $params['user_name']);
        }

        if (isset($params['operation']) and $params['operation']) {
            $this->db->where('uri', $params['operation']);
        }

        if (isset($params['date']) and $params['date']) {
            $this->db->where('date >=', $params['date']);
            $this->db->where('date <=', $params['date'] + 86400);
        }

        if (isset($params['namespace']) and $params['namespace']) {
            $this->db->where('namespace', $params['namespace']);
        }

        $query = $this->db->get($this->tableName);
        $result = $query->result_array(false);

        return $result;
    }
    // }}}
    // {{{ archive
    function archive($ids)
    {
        $row = array('archive' => 1);
        foreach ($ids as $id){
            $this->db->where('id', $id);
            $this->db->update($this->tableName, $row);
        }
    }
    // }}}
}
