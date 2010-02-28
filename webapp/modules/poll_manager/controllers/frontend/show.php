<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Show_Controller extends PollManager_Frontend
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        $this->poll_model = New Polls_Model();
    }
    // }}}
    // {{{ Index
    // {{{ index_read
    public function index_read($poll_id = Null, $result = False)
    {
        $choices     = Array();
        $vote_before = False;

        if (!isset($poll_id)) {
            $poll = $this->poll_model->getLatestPoll();
        } else {
            if (!$this->_has_poll($poll_id)) {
                $this->_invalid_request('poll_manager/frontend/show/index', _("Invalid poll id"));
            }
            $poll = $this->poll_model->getPoll($poll_id);
        }

        $total_votes = $this->poll_model->getTotalVotes($poll->id);

        if ($poll) {
            $choices     = $this->poll_model->getChoices($poll->id);
            $vote_before = $this->_vote_before($poll->id);
        }

        $this->layout->content = New View('frontend/polls', array(
                                                                  'poll'        => $poll,
                                                                  'choices'     => $choices,
                                                                  'total_votes' => $total_votes,
                                                                  'vote_before' => $vote_before,
                                                                  'show_result' => $result
                                                                 ));
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        $poll_choice = $this->input->post('poll_choice');
        $poll_id     = $this->input->post('poll_id');
        $ip          = $_SERVER['REMOTE_ADDR'];

        $this->poll_model->vote($poll_id, $poll_choice, $this->session->get('user.username'), $ip);

        cookie::set('polls_'.$poll_id, True, 31536000);

        url::redirect('poll_manager/frontend/show/index/'.$poll_id);
    }
    // }}}
    // {{{ index_validate_write
    public function index_validate_write()
    {
        $this->validation->name('poll_id', _("ID"))->add_rules('required', 'valid::numeric', array($this, '_has_poll'), array($this, '_vote_before'));
        $this->validation->name('poll_choice', _("Choice"))->add_rules('required', 'valid::numeric', array($this, '_has_choice'));
        $this->validation->name('captcha', _("Image's Text"))->add_rules('captcha', 'Captcha::valid', 'required');

        return $this->validation->validate();       
    }
    // }}}
    // {{{ index_write_error
    public function index_write_error()
    {
        $this->index_read($this->input->post('poll_id'));
    }
    // }}}
    // }}}
    // {{{ List
    // {{{ list_read
    public function list_read()
    {
        $polls = New Plist_Component('polls');
        $polls->setResource($this->poll_model->getPolls());
        $polls->addColumn('id', _("ID"));
        $polls->addColumn('title', _("Title"));
        $polls->addColumn('quiz', _("Quiz"));
        $polls->addColumn('Polls_Model::getDate[create_date]', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $polls->addAction('poll_manager/frontend/show/index/#id#/true', _("Result"), 'result_action');
        $polls->addAction('poll_manager/frontend/show/index/#id#', _("Vote"), 'vote_action');

        $this->layout->content = New View('frontend/list');
    }
    // }}}
    // }}}
    // {{{ _has_poll
    public function _has_poll($id)
    {
        $poll_model = New Polls_Model();

        return $poll_model->hasPoll($id);
    }
    // }}}
    // {{{ _vote_before
    public function _vote_before($id)
    {
        $session    = New Session();
        $poll_model = New Polls_Model();
    
            $vote_before_by_username = $session->get('user.authenticated', False)
                                     ? $poll_model->voteBeforeByUsername($id, $session->get('user.username'))
                                     : False;

            $cookie = $session->get('user.authenticated', False) ? False : cookie::get('polls_'.$id, False, True);

            return $vote_before_by_username || $cookie;
    }
    // }}}
    // {{{ _has_choice
    public function _has_choice($id)
    {
        $poll_model = New Polls_Model();

        return $poll_model->hasChoice($id, $this->input->post('poll_id'));
    }
    // }}}
}
