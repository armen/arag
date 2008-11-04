<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani<jilakhaghani@gmail.com>                           |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messaging_Model extends Model 
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();
        $this->tableName = 'messages';
    }
    // }}}
    // {{{ createMessage
    function createMessage($message_from, $message_to, $subject, $body, $creatDate, $read_status)
    {
        $row = array('message_from' => $message_from,
                     'message_to'   => $message_to,
                     'subject'      => $subject,
                     'body'         => $body,
                     'created_date' => $creatDate,
                     'read_status'  => $read_status);
    
        $this->db->insert($this->tableName,$row);               
    }
    // }}}
    // {{{ getMessages
    function getMessages($user_name,$id='')
    {
        $this->db->select('id, parrent_id, message_from, message_to, subject, body, created_date'); 
        if($id){
            $query  = $this->db->getWhere($this->tableName,array('id'=>$id, 'message_to' => $user_name)); 
        }else{
            $query  = $this->db->getWhere($this->tableName,array('message_to' => $user_name)); 
        }
        $result = $query->result_array(false);
        if($id){
            return current($result);
        }else{
            return $result;
        }
    }
    // }}}
    // {{{ getDate
    function getDate($row)
    {
        return format::date($row['created_date']);   
    }
    // }}}
    // {{{ getSentMessages
    function getSentMessages($user_name,$id='')
    {
        $this->db->select('id, parrent_id, message_from, message_to, subject, body, created_date'); 
        if($id){
            $query  = $this->db->getWhere($this->tableName,array('id' => $id, 'message_from' => $user_name)); 
        }else{
            $query  = $this->db->getWhere($this->tableName,array('message_from' => $user_name));
        } 
        $result = $query->result_array(false);
        if($id){
            return current($result);
        }else{
            return $result; 
        }
    }
    // }}}
}