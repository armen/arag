<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh emilsedgh@gmail.com>                                 |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class HelpManager_Model extends Model
{
    // {{{ Properties

    private $tableName       = 'helps';
    private $tableNameGroups = 'helps_groups';
    public  $dialogs         = Array();

    const BLANK              = 'blank';
    const ERROR              = 'error';
    const WARNING            = 'warning';
    const INFO               = 'info';
    const TIP                = 'tips';
    const COLLAPSIBLE        = 'collapsible';
    const _DEFAULT           = 'default';
    const _EMPTY             = 'empty';

    // }}}
    // {{{ __construct
    public function __construct()
    {
        parent::__construct();

        $this->dialogs = Array( HelpManager_Model::BLANK       => _("Blank"),
                                HelpManager_Model::ERROR       => _("Error"),
                                HelpManager_Model::WARNING     => _("Warning"),
                                HelpManager_Model::INFO        => _("Information"),
                                HelpManager_Model::TIP         => _("Tip"),
                                HelpManager_Model::COLLAPSIBLE => _("Collapsible"),
                                HelpManager_Model::_DEFAULT    => _("Default"),
                                HelpManager_Model::_EMPTY      => _("Empty"),
                              );
    }
    // }}}
    // {{{ decode
    public function decode($uri)
    {
        return str_replace('|', '/', $uri);
    }
    // }}}
    // {{{ encode
    public function encode($uri)
    {
        return str_replace('/', '|', $uri);
    }
    // }}}
    // {{{ getUris
    public function getUris()
    {
        $results = $this->db->select('uri')->from($this->tableName)->where('appname',APPNAME)->groupBy('uri')->get()->result();

        $uris = Array();
        foreach($results as $result) {
            $uris[] = array( 'decoded' => $this->decode($result->uri),
                             'encoded' => $result->uri
                           );
        }
        return $uris;
    }
    // }}}
    // {{{ add
    public function add($uri, $title, $message, $type)
    {
        $row = Array('uri'     => $this->encode($uri),
                     'title'   => $title,
                     'message' => $message,
                     'type'    => $type,
                     'appname' => APPNAME);

        return $this->db->insert($this->tableName, $row)->insert_id();
    }
    // }}}
    // {{{ edit
    public function edit($id, $title, $message, $type)
    {
        $row = Array('title'   => $title,
                     'message' => $message,
                     'type'    => $type);

       $this->db->where('id', $id);
       $this->db->where('appname', APPNAME)->update($this->tableName, $row);
    }
    // }}}
    // {{{ delete
    public function delete($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
        $this->db->delete($this->tableNameGroups, Array('help_id' => $id));
    }
    // }}}
    // {{{ get
    public function get($id)
    {
        return $this->db->select('id, title, uri, message, type')->from($this->tableName)
        ->where('id', $id)
        ->where('appname', APPNAME)
        ->get()->current();
    }
    // }}}
    // {{{ getByUri
    public function getByUri($uri, $listAll=false)
    {
        $old_current_uri     = Router::$current_uri;
        Router::$current_uri = Router::routed_uri($uri);
        Router::setup();

        $uri = implode('|', array_diff(Router::$segments, Router::$arguments));

        Router::$current_uri = $old_current_uri;
        Router::setup();

        $result = $this->db->select('id, title, uri, message, type')
                       ->from($this->tableName)
                       ->like('uri', $uri.'%', False)
                       ->where('appname', APPNAME)
                       ->get()->result(false);

        $helps = Array();
        foreach($result as $help) {
            if ($this->isAllowed($help['id'], Session::instance()->get('user.group_id')) || $listAll) {
                $helps[] = $help;
            }
        }
        return $helps;
    }
    // }}}
    // {{{ allow
    public function allow($id, $group_id)
    {
        if (!$this->isAllowed($id, $group_id)) {
            $this->db->insert($this->tableNameGroups, array('help_id'=>$id, 'group_id'=>$group_id));
        }
    }
    // }}}
    // {{{ deny
    public function deny($help_id, $group_id)
    {
        $this->db->delete($this->tableNameGroups, array('help_id'=>$help_id, 'group_id'=>$group_id));
    }
    // }}}
    // {{{ isAllowed
    public function isAllowed($help_id, $group_id)
    {
        $result = $this->db->select('count(*) as count')->from($this->tableNameGroups)->where(array('help_id'=>$help_id, 'group_id'=>$group_id))->get()->result()->current();
        return (bool) $result->count;
    }
    // }}}
    // {{{ translatedType
    public function translatedType($help)
    {
        return $this->dialogs[$help['type']];
    }
    // }}}
    // {{{ viewers
    public function viewers($help)
    {
        $allowed_list   = '';
        $groupsMan      = Model::load('Groups', 'user');
        $groups         = $groupsMan->getGroups();
        $allowed_groups = $this->db->select('group_id as id')->from($this->tableNameGroups)->where('help_id', $help['id'])->get()->result();

        foreach($allowed_groups as $allowed_group) {
            foreach($groups as $group) {
                if ($group['id'] == $allowed_group->id) {
                    $allowed_list .= $group['name'].' ';
                }
            }
        }
        return $allowed_list;
    }
    // }}}
}
