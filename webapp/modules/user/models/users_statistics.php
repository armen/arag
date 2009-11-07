<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Users_Statistics_Model extends Statistic_Model
{
    public function title()
    {
        return _("Registered users");
    }

    public function description()
    {
        return _("Number of registered users");
    }

    public function fetch($from = Null, $to = Null)
    {
        $this->db->select('count(*) as count')->from('user_users');
        if ($from) {
            $this->db->where('create_date >', $from);
        }

        if ($to) {
            $this->db->where('create_date <', $to);
        }
        $all = (int)$this->db->get()->current()->count;

        return Array('registered' => $all);
    }

    public function interval()
    {
        return self::DAY;
    }

    public function series()
    {
        return array('registered' => _("Registered"));
    }
}
