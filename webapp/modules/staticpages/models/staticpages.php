<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author:   Sasan Rose <sasan.rose@gmail.com>                             |
// |           Jila Khaghani <jilakhaghani@gmail.com>                       |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class StaticPages_Model extends Model
{
    // {{{ Properties

    public $tableName;
    private $cache_id;

    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableName = 'static_pages';
        $this->cache_id   = APPNAME.'_get_routes';
    }
   // }}}
    // {{{ createPage
    public function createPage($author, $subject, $page, $route)
    {
        $row = Array('author'      => $author,
                     'create_date' => time(),
                     'modify_date' => time(),
                     'subject'     => $subject,
                     'page'        => $page,
                     'appname'     => APPNAME,
                     'route'       => $route);

        $this->db->insert($this->tableName, $row);

        $cache = new Cache;
        $cache->delete($this->cache_id);
    }
    // }}}
    // {{{ editPage
    public function editPage($id, $subject, $page, $route)
    {
         $entry = $this->getPage($id);

         $row = Array('subject'     => $subject,
                      'page'        => $page,
                      'author'      => $entry['author'],
                      'create_date' => $entry['create_date'],
                      'modify_date' => time(),
                      'route'       => $route);

        $this->db->where('id', $id)
             ->where('appname', APPNAME);
        $this->db->update($this->tableName, $row);

        $cache = new Cache;
        $cache->delete($this->cache_id);
    }
    // }}}
    // {{{ deletePage
    public function deletePage($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));

        $cache = new Cache;
        $cache->delete($this->cache_id);
    }
    // }}}
    // {{{ & getPage
    public function & getPage($id)
    {
        $this->db->select('id, subject, page, author, create_date, route');
        $this->db->from($this->tableName);
        $this->db->where('id', $id);

        $query = $this->db->get();
        $row = (Array) $query->current();

        return $row;
    }
    // }}}
    // {{{ & getPages
    public function & getPages()
    {
        $this->db->select('id, subject, author, create_date, modify_date, route');
        $this->db->where('appname', APPNAME);
        $this->db->orderby('create_date', 'desc');
        $query = $this->db->get($this->tableName);

        $retval = $query->result_array(False);
        return $retval;
    }
    // }}}
    // {{{ getDate
    public function getDate($row)
    {
        return format::date($row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    public function getModifyDate($row)
    {
        return format::date($row['modify_date']);
    }
    // }}}
    // {{{ checkID
    public function checkID($id)
    {
        $this->db->select('id');
        $query = $this->db->getwhere($this->tableName, Array('id' => $id, 'appname' => APPNAME));
        return (boolean)count($query);
    }
    // }}}
    // {{{ getRoutes
    public function getRoutes()
    {
        $cache  = new Cache;
        $cached = $cache->get($this->cache_id);
        if ($cached) {
            return $cached;
        }

        $results = $this->db->select('id, route')->from($this->tableName)->where('appname', APPNAME)
                       ->get()->result_array(false);

        $routes = array();

        foreach ($results as $result ) {
            $routes[$result['route']] = 'staticpages/frontend/view/'.$result['id'];
        }

        $cache->set($this->cache_id, $routes);
        return $routes;
    }
    // }}}
}

?>
