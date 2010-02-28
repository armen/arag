<?php
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Polls_Model extends Model
{
    // {{{ __construct
    function __construct()
    {
        parent::__construct();

        $this->tablePolls        = 'polls';
        $this->tablePollsChoices = 'polls_choices';
        $this->tablePollsUsers   = 'polls_users';
    }
    // }}}
    // {{{ getPolls
    public function getPolls()
    {
        return $this->db->select('*')->from($this->tablePolls)->get()->result_array(false);
    }
    // }}}
    // {{{ getPoll
    public function getPoll($id)
    {
        return $this->db->select('*')->from($this->tablePolls)->where('id', $id)->get()->current();
    }
    // }}}
    // {{{ getLatestPoll
    public function getLatestPoll()
    {
        return $this->db->select('*')->from($this->tablePolls)->limit(1)->orderby('id', 'DESC')->get()->current();
    }
    // }}}
    // {{{ getTotalVotes
    public function getTotalVotes($pollId)
    {
        return $this->db->select('sum(number) as sum')->from($this->tablePollsChoices)->where('poll_id', $pollId)->get()->current()->sum;
    }
    // }}}
    // {{{ getChoices
    public function getChoices($pollId) {
        return $this->db->select('*')->from($this->tablePollsChoices)->where('poll_id', $pollId)->get()->result_array(false);
    }
    // }}}
    // {{{ getChoice
    public function getChoice($id) {
        return $this->db->select('*')->from($this->tablePollsChoices)->where('id', $id)->get()->current();
    }
    // }}}
    // {{{ hasPoll
    public function hasPoll($id)
    {
        return (boolean) $this->db->select('count(id) as count')->from($this->tablePolls)->where('id', $id)->get()->current()->count;
    }
    // }}}
    // {{{ hasChoice
    public function hasChoice($id, $pollId)
    {
        return (boolean) $this->db->select('count(id) as count')->from($this->tablePollsChoices)->where('id', $id)->where('poll_id', $pollId)->get()->current()->count;
    }
    // }}}
    // {{{ insert
    public function insert($title, $quiz)
    {
        $session = New Session();

        $row = Array(
                     'title'       => $title,
                     'quiz'        => $quiz,
                     'create_date' => time(),
                     'created_by'  => $session->get('user.username')
                    );

        $this->db->insert($this->tablePolls, $row);
    }
    // }}}
    // {{{ updatePoll
    public function updatePoll($id, $title, $quiz)
    {
        $session = New Session();

        $row = Array(
                     'title'       => $title,
                     'quiz'        => $quiz,
                    );

        $this->db->where('id', $id);
        $this->db->update($this->tablePolls, $row);
    }
    // }}}
    // {{{ insertChoice
    public function insertChoice($title, $pollId, $color)
    {
        $session = New Session();

        $row = Array(
                     'title'   => $title,
                     'poll_id' => $pollId,
                     'number'  => 0,
                     'color'   => $color
                    );

        $this->db->insert($this->tablePollsChoices, $row);
    }
    // }}}
    // {{{ deleteChoice
    public function deleteChoice($id)
    {
        $choice = $this->getChoice($id);
        $this->db->delete($this->tablePollsChoices, array('id' => $id));
        $this->db->delete($this->tablePollsUsers, array('choice_id' => $id));
        
        return $choice;
    }
    // }}}
    // {{{ vote
    public function vote($pollId, $choiceId, $username, $ip)
    {
        $row = Array (
                      'poll_id'   => $pollId,
                      'choice_id' => $choiceId,
                      'username'  => $username,
                      'ip'        => $ip
                     );

        $this->db->insert($this->tablePollsUsers, $row);

        $choice = $this->getChoice($choiceId);
        
        $this->db->where('id', $choiceId);
        $this->db->update($this->tablePollsChoices, Array('number' => $choice->number + 1));
    }
    // }}}
    // {{{ voteBeforeByUsername
    public function voteBeforeByUsername($pollId, $username)
    {
        return (boolean) $this->db->select('count(*) as count')->from($this->tablePollsUsers)->where('poll_id', $pollId)->where('username', $username)->get()->current()->count;
    }
    // }}}
    // {{{ voteBeforeByIp
    public function voteBeforeByIp($pollId, $ip)
    {
        return (boolean) $this->db->select('count(*) as count')->from($this->tablePollsUsers)->where('poll_id', $pollId)->where('ip', $ip)->get()->current()->count;
    }
    // }}}
    // {{{ List callbacks
    // {{{ getDate
    public function getDate($row, $field)
    {
        return format::date($row[$field]);
    }
    // }}}
    // {{{ getColor
    public function getColor($row)
    {
        $view = New View('backend/color', array('color' => $row['color']));
        return $view->render();
    }
    // }}}
    // }}}
}
