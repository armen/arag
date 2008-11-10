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
    function createMessage($message_from, $message_to, $subject, $body, $creatDate, $read_status, $parrent_id=null)
    {
        $row = array('parrent_id'   => $parrent_id,
                     'message_from' => $message_from,
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
        $this->db->select('id, parrent_id, message_from, message_to, subject, body, created_date, read_status');
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
        $this->db->select('id, parrent_id, message_from, message_to, subject, body, created_date, read_status');
        if($id){
            $query  = $this->db->getWhere($this->tableName, array('id' => $id, 'message_from' => $user_name));
        }else{
            $query  = $this->db->getWhere($this->tableName, array('message_from' => $user_name));
        }
        $result = $query->result_array(false);
        if($id){
            return current($result);
        }else{
            return $result;
        }
    }
    // }}}
    // {{{ updateStatuse
    function updateStatuse($id)
    {
        $this->db->where('id',$id);
        $row = array( 'read_status' => 1);
        $this->db->update($this->tableName, $row);
    }
    // }}}
    // {{{ updateStatuseToUnread
    function updateStatuseToUnread($id)
    {
       $this->db->where('id',$id);
       $row = array( 'read_status' => 0);
       $this->db->update($this->tableName, $row);
    }
    // }}}
    // {{{ getMessageSubject
    function getMessageSubject($id)
    {
        $this->db->select('subject');
        $query  = $this->db->get($this->tableName, array( 'id' => $id ));
        $result = $query->current()->subject;
        return  $result;
    }
    // }}}
    // {{{ checkMessageTo
    function checkMessageTo($id, $user_name)
    {
        $this->db->select('count(id) as count');
        $query  = $this->db->getwhere($this->tableName, array( 'id' => $id , 'message_to' => $user_name));
        $result = (boolean)$query->current()->count;
        return  $result;
    }
    // }}}
    // {{{ deleteMessage
    function deleteMessage($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ checkMessageFrom
    function checkMessageFrom($id, $user_name)
    {
        $this->db->select('count(id) as count');
        $query  = $this->db->getwhere($this->tableName, array( 'id' => $id , 'message_from' => $user_name));
        $result = (boolean)$query->current()->count;
        return  $result;
    }
    // }}}
}