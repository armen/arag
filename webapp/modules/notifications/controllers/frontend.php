<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller
{
    // {{{ __construct
    public function __construct()
    {
        parent::__construct();

        $this->layout->page_title = 'Notifications';

        $this->message = new Notification_Model();
    }
    // }}}
    // {{{ index_read
    public function index_read($type = 'sms')
    {
        $notificatins = new PList_Component('notificatins');
        $notificatins->setResource($this->message->show($this->session->get('user.username'),Notification_Model::ALL));
        $notificatins->setLimit(Arag_Config::get('limit',0));
        $notificatins->addColumn('Frontend_Controller::_link_bold_title', _("Subject"), PList_Component::VIRTUAL_COLUMN );
        $notificatins->addColumn('Frontend_Controller::_display_date', _("Date"), PList_Component::VIRTUAL_COLUMN );
        $notificatins->addColumn('created_by', _("Created By"));

        $this->layout->content = new View('frontend');
    }
    // }}}
    // {{{ _link_bold_subject
    function _link_bold_title($row)
    {
        if($row['title'] == '') {
            $row['title'] = substr($row['description'],0,200);
        }

        $class      = ($row['icon']) ? $row['icon'] : (($row['type']) ? $row['type'] : Notification_MOdel::NOTIFICATION_DEFAULT);
        $properties = array('title' => $row['description'], 'class' => $class.' notification_icon');

        if ($row['visited'] == 0) {
            return  html::anchor(urlencode($row['uri']), $row['title'],$properties);
        }

       return "<b>".html::anchor(urlencode($row['uri']), $row['title'], $properties)."</b>";
    }
    // }}}
    // {{{ _display_date
    function _display_date($row)
    {
        return format::date($row['create_date']);
    }
    // }}}
    // {{{ webservice_send_sms
    function webservice_send_sms($msg)
    {
        (PHP_SAPI != 'cli') AND die("Invalid Request!");

        $this->message->setMessage($msg);
        $this->message->sms();
    }
    // }}}
}
