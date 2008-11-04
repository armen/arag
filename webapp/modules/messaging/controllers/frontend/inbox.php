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
        $messages->addAction('messaging/frontend/inbox/message_body/#id#','view','view_action');
        
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
        $read_status  = 0;

        $this->message->createMessage($message_from, $message_to, $subject, $body, $creatDate, $read_status);
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
        $sent_messages->addAction('messaging/frontend/inbox/sent_message_body/#id#','view','view_action');
        
        $this->layout->content = new View('frontend/sent');
    }
    // }}}
    // {{{ message_body
    function message_body($id)
    {
        $message_body = $this->message->getMessages($this->session->get('user.username'),$id);
        $this->layout->content = new View('frontend/message_body');
        
    }
    // }}}
    // {{{ sent_message_body
    function sent_message_body($id)
    {
        $message_body = $this->message->getSentMessages($this->session->get('user.username'),$id);
        $this->layout->content = new View('frontend/message_body');
    }
    // }}}
}