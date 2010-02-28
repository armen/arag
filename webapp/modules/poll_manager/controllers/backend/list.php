<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class List_Controller extends PollManager_Backend
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
    public function index_read()
    {
        $polls = New Plist_Component('polls');
        $polls->setResource($this->poll_model->getPolls());
        $polls->addColumn('id', _("ID"));
        $polls->addColumn('title', _("Title"));
        $polls->addColumn('quiz', _("Quiz"));
        $polls->addColumn('created_by', _("Created By"));
        $polls->addColumn('Polls_Model::getDate[create_date]', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $polls->addAction('poll_manager/backend/list/edit/#id#', _("Edit"), 'edit_action');
        $polls->addAction('poll_manager/frontend/show/index/#id#/true', _("Result"), 'result_action');


        $this->layout->content = New View('backend/polls', array('saved' => $this->session->get_once('polls.changed', False)));
    }
    // }}}
    // }}}
    // {{{ Add
    // {{{ add_read
    public function add_read()
    {
        $this->layout->content = New View('backend/add');
    }
    // }}}
    // {{{ add_write
    public function add_write()
    {
        $title = $this->input->post('title');
        $quiz  = $this->input->post('quiz');

        $this->poll_model->insert($title, $quiz);

        $this->session->set('polls.changed', True);

        url::redirect('poll_manager/backend/list');
    }
    // }}}
    // }}}
    // {{{ Edit
    // {{{ edit_read
    public function edit_read($id)
    {
        $this->global_tabs->addItem(_("Edit"), 'poll_manager/backend/list/edit/'.$id, 'poll_manager/backend/list/');

        $poll = $this->poll_model->getPoll($id);

        $choices = New Plist_Component('choices');
        $choices->setResource($this->poll_model->getChoices($id));
        $choices->addColumn('id', _("ID"));
        $choices->addColumn('title', _("Title"));
        $choices->addColumn('number', _("Result"));
        $choices->addColumn('Polls_Model::getColor', _("Color"), PList_Component::VIRTUAL_COLUMN);
        $choices->addAction('poll_manager/backend/list/delete_choice/#id#', _("Delete"), 'delete_action');

        $this->layout->content = New View('backend/edit', Array('poll' => $poll, 'saved' => $this->session->get_once('polls.changed', False)));
    }
    // }}}
    // {{{ edit_write
    public function edit_write()
    {
        $title = $this->input->post('title');
        $quiz  = $this->input->post('quiz');
        $id    = $this->input->post('id');

        $this->poll_model->updatePoll($id, $title, $quiz);

        $this->session->set('polls.changed', True);

        url::redirect('poll_manager/backend/list/edit/'.$id);
    }
    // }}}
    // }}}
    // {{{ Choices
    // {{{ add_choice_write
    public function add_choice_write()
    {
        $title   = $this->input->post('title');
        $poll_id = $this->input->post('poll_id');
        $color   = $this->input->post('color', '#000000');

        $this->poll_model->insertChoice($title, $poll_id, $color);

        $this->session->set('polls.changed', True);

        url::redirect('poll_manager/backend/list/edit/'.$poll_id);
    }
    // }}}
    // {{{ delete_choice_read
    public function delete_choice_read($id)
    {
        $choice = $this->poll_model->deleteChoice($id);

        url::redirect('poll_manager/backend/list/edit/'.$choice->poll_id);
    }
    // }}}
    // }}}
}
