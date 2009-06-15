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
        $archive_status = $this->input->post('archive_status', $this->session->get_once('archive_status'), 0);
        $user_name      = $this->input->post('username', $this->session->get_once('username'), null);
        $operation      = $this->input->post('operation', $this->session->get_once('operation'), null);
        $from           = $this->input->post('from', $this->session->get_once('from'), null);
        $to             = $this->input->post('to', $this->session->get_once('to'), null);

        $datefrom       = date::strtotime($from);
        $dateto         = date::strtotime($to);

        if ($datefrom && $datefrom === $dateto) {
             $dateto += 86400;
        }

        if ($datefrom && $dateto && $datefrom > $dateto) {
             $temp       = $dateto;
             $dateto     = $datefrom;
             $datefrom   = $temp;

             $temp = $to;
             $to   = $from;
             $from = $temp;
        }

        $appname = (MASTERAPP) ? $this->session->get('agency_appname') : APPNAME;

        $logs    = $this->logger->search($archive_status, $user_name, $operation, $datefrom, $dateto, $appname);

        $archive_array = array(0 => _("Non Archived"), 1 => _("Archived"));

        $logs_list = new PList_Component('logs_list');
        $logs_list->setResource($logs);
        $logs_list->setLimit(20, 0);
        $logs_list->addColumn('Backend_Controller::show_operation', _("Operation"), PList_Component::VIRTUAL_COLUMN);
        $logs_list->addColumn('owner', _("User"));
        $logs_list->addColumn('namespace', _("For"));
        $logs_list->addColumn('format::show_date[date, true]', _("Date"), PList_Component::VIRTUAL_COLUMN);
        $logs_list->addAction('logger/backend/archive', _("Archive"), 'edit_action',  False, PList_Component::GROUP_ACTION);
        $logs_list->setGroupActionParameterName('id');
        $logs_list->setEmptyListMessage(_("Nothing Found!"));

        $this->layout->content           = new View('backend/logs_list');
        $this->layout->content->massages = $this->messages;
        $this->layout->content->archive  = $archive_array;

        $this->session->set('username', $user_name);
        $this->session->set('from', $from);
        $this->session->set('to', $to);
        $this->session->set('operation', $operation);
        $this->session->set('archive_status', $archive_status);


        $this->layout->content->archive_status = $archive_status;
        $this->layout->content->from           = $datefrom;
        $this->layout->content->to             = $dateto;
        $this->layout->content->username       = $user_name;
        $this->layout->content->operation      = $operation;
    }
    // }}}
    // {{{ show_operation
    function show_operation($row)
    {
        $messages = Arag_Config::get('logger.messages', Null, 'logger', True);
        return _($messages[$row['uri']]);
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
