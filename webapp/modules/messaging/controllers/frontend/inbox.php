<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani<jilakhaghani@gmail.com>                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Inbox_Controller extends Messaging_Frontend
{
	public function index()
	{
		$this->layout->content = new View('frontend/inbox');
		
	}
	
	public function new_message_read()
	{
		$this->layout->content = new View('frontend/new_message');
		$this->users = Model::load('users','user');
		$usersList = $this->users->getUsers($id=NULL, $appname = NULL, $groupname = NULL, $user = NULL, $flagappname = true);
		
		$userOptions = array();
		$userOptions[] = '';
		foreach ($usersList as $user){
			$userOptions[$user['username']] = $user['user_name'];
		}
		
		$this->layout->userOptions = $userOptions;
	}
	
	public function new_message_validate_write()
	{   
		$this->validation->name('username',_("Message To"))->add_rules('username','required');
		$this->validation->name('subject',_("subject"))->add_rules('subject','required');
		
		return $this->validation->validate();
		
	}
	
	function new_message_write_error()
	{
		$this->new_message_read();
	}
	
	function new_message_write()
	{
		$creatDate    = time();
		$message_from = $this->userName;
		$message_to   = $this->input->post('username');
		$subject      = $this->input->post('subject');
		$body 		  = $this->input->post('body');
		$read_status  = 'unread';

		$this->Message->createMessage($message_from,$message_to,$subject,$body,$creatDate,$read_status);
		url::redirect('messaging/frontend/inbox');
		
	}
	
}