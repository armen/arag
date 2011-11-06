<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Maryam Talebi <mym.talebi@gmail.com>                            |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ __construct
    public function __construct()
    {
        parent::__construct();

        $this->layout->page_title = 'Notifications';

        $this->message = Model::load('Notification', 'notifications');

        $this->validation->message('required',_("%s is required."));
        $this->validation->message('_check_to',_("Invalid username or cellphone number."));
        $this->validation->message('email',_("Invalid E-mail."));

        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Notifications"));
        $this->global_tabs->addItem(_("SMS"), "notifications/backend/index/sms");
        $this->global_tabs->addItem(_("E-Mail"), "notifications/backend/index/email");
    }
    // }}}
    // {{{ index_read
    public function index_read($type = 'sms')
    {
        $this->layout->content = new View('backend',array(
            'type'    => $type,
            'message' => $this->session->get_once('notification_message'),
            'error'   => $this->session->get_once('notification_error')
        ));
    }
    // }}}
    // {{{ index_validate_write
    public function index_validate_write($type)
    {
        $this->validation->name('to', _("To"))->add_rules('to', 'required', ($type == 'sms') ? array($this, '_check_to') : 'valid::email' );

        if($type == 'email') {
            $this->validation->name('subject', _("Subject"))->add_rules('subject', 'required');
        }

        return $this->validation->validate();
    }
    // }}}
    // {{{ index_validate_write
    public function index_write_error($type)
    {
        $this->index_read($type);
    }
    // }}}
    // {{{ _check_to
    public function _check_to($to)
    {
        //check if "to" field is valid

        $is_cellphone = preg_match('/^09[13][23456789][0-9]{7}$/', $to);

        $user_model   = Model::load('Users', 'user');
        $is_user      = $user_model->hasUserName($to);

        return ($is_cellphone || $is_user);
    }
    // }}}
    // {{{ index_write
    public function index_write($type)
    {
        if($type == 'sms') {
            $to = $this->input->post('to');
            $this->message->setMessage($this->input->post('body'));

            $sent_ok = $this->message->sms($to);
        } elseif($type == 'email') {
            $recipients = $this->input->post('to');
            $subject    = $this->input->post('subject');
            $this->message->setMessage($this->input->post('body'));

            $sent_ok = $this->message->email($recipients, $subject);
        }

        if($sent_ok) {
            $this->session->set_flash('notification_message', _("The message has been sent successfully."));
        } else {
            $this->session->set_flash('notification_error', _("Sending message encountered a problem.Please try again later."));
        }

        url::redirect("notifications/backend/index/$type");
    }
    // }}}

}
