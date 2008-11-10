<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani<jilakhaghani@gmail.com>                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Inbox_Controller extends Messaging_Frontend
{
    // {{{ index
    public function index()
    {
        $messages = new PList_Component('messages');
        $messages->setResource($this->message->getMessages($this->session->get('user.username')));
        $messages->setLimit(Arag_Config::get('limit',0));
        $messages->addColumn('Messaging.getDate', _("Created Date"), PList_Component::VIRTUAL_COLUMN );
        $messages->addColumn('message_from', _("From"));
        $messages->addColumn('subject', _("Subject"));
        $messages->addAction('messaging/frontend/inbox/message_body/#id#', _("Body"), 'view_action');
        $messages->addAction('messaging/frontend/inbox/delete/#id#', _("Delete"), 'delete_action');
        $messages->addAction('Inbox_Controller::show_status');

        $this->layout->content = new View('frontend/inbox');
    }
    // }}}
    // {{{ new_message
    // {{{ new_message_read
    public function new_message_read()
    {
        $users     = Model::load('users','user');
        $usersList = $users->getUsers(NULL, NULL, NULL, NULL, true);

        $userOptions = array();

        foreach ($usersList as $user){
            $userOptions[$user['username']] = $user['user_name'];
        }

        $this->layout->content      = new View('frontend/new_message');
        $this->layout->user_options = $userOptions;
    }
    // }}}
    // {{{ new_message_validate_write
    public function new_message_validate_write()
    {
        $this->validation->name('username', _("To"))->add_rules('username', 'required');
        $this->validation->name('subject', _("Subject"))->add_rules('subject', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ new_message_write_error
    function new_message_write_error()
    {
        $this->new_message_read();
    }
    // }}}
    // {{{ new_message_write
    function new_message_write()
    {
        $creatDate    = time();
        $message_from = $this->session->get('user.username');
        $message_to   = $this->input->post('username');
        $subject      = $this->input->post('subject');
        $body         = $this->input->post('body');
        $parrent_id   = $this->input->post('parrent_id');
        $read_status  = 0;

        $this->message->createMessage($message_from, $message_to, $subject, $body, $creatDate, $read_status, $parrent_id);
        url::redirect('messaging/frontend/inbox');
    }
    // }}}
    // }}}
    // {{{ sent_messages
    function sent_messages()
    {
        $sent_messages = new PList_Component('sent_messages');
        $sent_messages->setResource($this->message->getSentMessages($this->session->get('user.username')));
        $sent_messages->addColumn('Messaging.getDate', _("Created Date"), PList_Component::VIRTUAL_COLUMN );
        $sent_messages->addColumn('message_to', _("To"));
        $sent_messages->addColumn('subject', _("Subject"));
        $sent_messages->addAction('messaging/frontend/inbox/sent_message_body/#id#', 'view', 'view_action');
        $sent_messages->addAction('messaging/frontend/inbox/delete_sent/#id#', _("Delete"), 'delete_action');
        $sent_messages->addAction('Inbox_Controller::show_status');
        $sent_messages->setEmptyListMessage(_("There is no message!"));

        $this->layout->content = new View('frontend/sent');
    }
    // }}}
    // {{{ message_body
    function message_body($id)
    {
        $this->global_tabs->addItem(_("Body"), "messaging/frontend/inbox/message_body", "messaging/frontend/inbox");
        $this->global_tabs->addItem(_("Reply"), "messaging/frontend/inbox/reply/$id", "messaging/frontend/inbox");

        $this->message->updateStatuse($id);

        $message_body = $this->message->getMessages($this->session->get('user.username'), $id);
        $this->layout->content = new View('frontend/message_body', $message_body);
    }
    // }}}
    // {{{ sent_message_body
    function sent_message_body($id)
    {
        $this->global_tabs->addItem(_("Body"), "messaging/frontend/inbox/sent_message_body", 'messaging/frontend/inbox/sent_messages');

        $this->message->updateStatuse($id);

        $message_body = $this->message->getSentMessages($this->session->get('user.username'), $id);
        $this->layout->content = new View('frontend/message_body', $message_body);
    }
    // }}}
    // {{{ delete
    // {{{ delete_read
    function delete_read($id)
    {
        $this->global_tabs->addItem(_("Delete"), 'messaging/frontend/inbox/delete', 'messaging/frontend/inbox');

        $subject = $this->message->getMessageSubject($id);
        $data    = array( 'id' => $id, 'subject' => $subject );

        $this->layout->content = new View('frontend/delete', $data);
    }
    // }}}
    // {{{ delete_validate_read
    function delete_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, 'check_message'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_read_error
    function delete_read_error()
    {
        $this->_invalid_request('messaging/frontend/inbox', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_validate_write
    function delete_validate_write()
    {
        $this->validation->name('id', _("ID"))->add_rules('id', 'required', 'valid::numeric', array($this, 'check_message'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_read_error
    function delete_write_error()
    {
        $this->_invalid_request('messaging/frontend/inbox', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_write
    function delete_write()
    {
        $id = $this->input->post('id');
        $this->message->deleteMessage($id);
        url::redirect('messaging/frontend/inbox');
    }
    // }}}
    // }}}
    // {{{ delete_sent
    // {{{ delete_sent_read
    function delete_sent_read($id)
    {
        $this->global_tabs->addItem(_("Delete"), 'messaging/frontend/inbox/delete_sent', 'messaging/frontend/inbox/sent_messages');

        $subject = $this->message->getMessageSubject($id);
        $data    = array( 'id' => $id, 'subject' => $subject );

        $this->layout->content = new View('frontend/delete_sent', $data);
    }
    // }}}
    // {{{ delete_sent_validate_read
    function delete_sent_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, 'check_sent_message'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_sent_read_error
    function delete_sent_read_error()
    {
        $this->_invalid_request('messaging/frontend/inbox/sent_messages', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_sent_validate_write
    function delete_sent_validate_write()
    {
        $this->validation->name('id', _("ID"))->add_rules('id', 'required', 'valid::numeric', array($this, 'check_sent_message'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_sent_write_error
    function delete_sent_write_error()
    {
        $this->_invalid_request('messaging/frontend/inbox/sent_messages', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_sent_write
    function delete_sent_write()
    {
        $id = $this->input->post('id');
        $this->message->deleteMessage($id);
        url::redirect('messaging/frontend/inbox/sent_messages');
    }
    // }}}
    // }}}
    // {{{ check_sent_message
    function check_sent_message($id)
    {
        return  $this->message->checkMessageFrom($id, $this->session->get('user.username'));
    }
    // }}}
    // {{{ reply
    // {{{ reply_read
    function reply_read($id)
    {
        $this->global_tabs->addItem(_("Reply"), "messaging/frontend/inbox/reply/$id", "messaging/frontend/inbox");

        $message = $this->message->getMessages($this->session->get('user.username'), $id);
        $this->layout->content = new View('frontend/reply', $message);
    }
    // }}}
    // {{{ reply_validate_read
    function reply_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this, 'check_message'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ reply_read_error
    function reply_read_error()
    {
        $this->_invalid_request('messaging/frontend/inbox', _("Invalid ID"));
    }
    // }}}
    // {{{ check_message
    function check_message($id)
    {
       return  $this->message->checkMessageTo($id, $this->session->get('user.username'));
    }
    // }}}
    // {{{ show_status
    function show_status($message)
    {
        if($message['read_status']==1){
            return Array( 'uri'       => "messaging/frontend/inbox/change_status/".$message['id']."/".$message['read_status'],
                          'label'     => _("Change read status"),
                          'className' => 'apply_action' );
        }else{
            return Array( 'uri'       => "messaging/frontend/inbox/change_status/".$message['id']."/".$message['read_status'],
                          'label'     => _("Change read status"),
                          'className' => 'apply_action_alt' );
        }

    }
    // }}}
    // {{{ change_status
    function change_status($id, $status)
    {
        if($status==1){
             $this->message->updateStatuseToUnread($id);
        }else{
             $this->message->updateStatuse($id);
        }
        url::redirect('messaging/frontend/inbox');
    }
    // }}}
 }