<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani <jilakhaghani@gmail.com>                          |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------
class Backend_Controller extends Logger_Backend
{
    // {{{ index
    function index_any()
    {
        $archive_status = $this->input->post('archive_status', 0);
        $user_name      = $this->input->post('username', null);
        $operation      = $this->input->post('operation', null);
        $date           = date::get_time('date');

        $logs = $this->logger->search($archive_status, $user_name, $operation, $date);

        $archive_array = array(0 => _("Non Archived"), 1 => _("Archived"));

        $logs_list = new PList_Component('logs_list');
        $logs_list->setResource($logs);
        $logs_list->addColumn('Backend_Controller::show_operation', _("Operation"), PList_Component::VIRTUAL_COLUMN);
        $logs_list->addColumn('owner', _("User"));
        $logs_list->addColumn('Backend_Controller::show_date', _("Date"), PList_Component::VIRTUAL_COLUMN);
        $logs_list->addAction('logger/backend/archive', _("Archive"), 'edit_action',  False, PList_Component::GROUP_ACTION);
        $logs_list->setGroupActionParameterName('id');
        $logs_list->setEmptyListMessage(_("Nothing Found!"));

        $this->layout->content  = new View('backend/logs_list');
        $this->layout->massages = $this->messages;
        $this->layout->archive  = $archive_array;

        $this->layout->archive_status = $archive_status;
        $this->layout->user_name      = $user_name;
        $this->layout->date           = $this->input->post('date', null);
        $this->layout->operation      = $operation;
    }
    // }}}
    // {{{ show_operation
    function show_operation($row)
    {
        $messages = Arag_Config::get('logger.messages', Null, 'logger', True);
        return  $messages[$row['uri']];
    }
    // }}}
    // {{{
    function show_date($row)
    {
        return format::date($row['date']);
    }
    // }}}
    // {{{ archive
    // {{{ archive_write
    function archive_write()
    {
        $ids = $this->input->post('id');
        $this->logger->archive($ids);
        url::redirect('logger/backend');
    }
    // }}}
    // {{{ archive_validate_write
    function archive_validate_write()
    {
        $this->validation->name('id', _("ID"))->add_rules('id', 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ archive_write_error
    function  archive_write_error()
    {
        $this->index_any();
    }
    // }}}
    // }}}
}