<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani <jilakhaghani@gmail.com>                          |
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
    function createMessage($message_from, $message_to, $subject, $body, $creatDate, $read_status, $parent_id=null)
    {
        $row = array('parent_id'    => $parent_id,
                     'message_from' => $message_from,
                     'message_to'   => $message_to,
                     'subject'      => $subject,
                     'body'         => $body,
                     'created_date' => $creatDate,
                     'read_status'  => $read_status);

        $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ getMessages
    function getMessages($user_name, $id = '')
    {
        $this->db->select('id, parent_id, message_from, message_to, subject, body, created_date, read_status')
                 ->from($this->tableName)->where('message_to',$user_name)->orderby('created_date', 'DESC');

        if ($id) {
            $this->db->where('id', $id);
        }

        $result = $this->db->get()->result_array(false);

        if ($id) {
            return current($result);
        }

        return $result;
    }
    // }}}
    // {{{ getDate
    function getDate($row)
    {
        return format::date($row['created_date']);
    }
    // }}}
    // {{{ getSentMessages
    function getSentMessages($user_name, $id = '')
    {
        $this->db->select('id, parent_id, message_from, message_to, subject, body, created_date, read_status')
                  ->from($this->tableName)->where('message_from', $user_name)->orderby('created_date', 'DESC');

        if ($id) {
             $this->db->where('id', $id);
        }

        $result = $this->db->get()->result_array(false);

        if ($id) {
            return current($result);
        }

        return $result;
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
    // {{{ getReadMessagesCount
    public function getReadMessagesCount($username)
    {
        return (int) $this->db->select('count(id) as count')->from($this->tableName)->where('read_status', 0)->where('message_to', $username)->get()->current()->count;
    }
    // }}}
}
