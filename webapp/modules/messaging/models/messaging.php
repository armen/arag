<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani<jilakhaghani@gmail.com>                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messaging_Model extends Model 
{
	function __construct()
	{
		parent::__construct();
		$this->tableName = 'messages';
	}
	
	function createMessage($message_from,$message_to,$subject,$body,$creatDate,$read_status)
	{
		$row = array(  'message_from' => $message_from,
					   'message_to'	  => $message_to,
					   'subject' 	  => $subject,
					   'body'		  => $body,
					   'created_date' => $creatDate,
					   'read_status'  => $read_status);
	
		$this->db->insert($this->tableName,$row);			   
	}
}
?>