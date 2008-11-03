<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani<jilakhaghani@gmail.com>                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------
class Messaging_Frontend extends Controller 
{
	public  $userName;
	
	public function __construct()
	{
		parent::__construct();
		
		// Default page title
        $this->layout->page_title = 'Message';
               
        $this->validation->message('required',_("%s is required"));
        
        $this->userName = $_SESSION['user']['username'];
        //Load the Model
        $this->Message = new Messaging_Model();
        
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Message"));
        $this->global_tabs->addItem(_("Inbox"),'messaging/frontend/inbox');
        $this->global_tabs->addItem(_("New Message"),'messaging/frontend/inbox/new_message');
        
		
	}
	
}
?>