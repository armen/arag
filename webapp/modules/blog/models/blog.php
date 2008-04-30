<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Blog_Model extends Model 
{
    // {{{ Properties
    
    public $tableName;

    const PROP_PUBLISH             = 1;
    const PROP_ALLOW_COMMENTS      = 2;
    const PROP_REQUIRES_MODERATION = 4;

    // }}}
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableName = 'blog_entries';
    }
    // }}}
    // {{{ createEntry
    public function createEntry($subject, $entry, $extendedEntry, $author, $published, $allowComments, $requiresModeration, $category)
    {
        $row = Array('subject'             => $subject, 
                     'entry'               => $entry, 
                     'extended_entry'      => $extendedEntry,
                     'author'              => $author,
                     'create_date'         => time(),
                     'modify_date'         => 0,
                     'modified_by'         => Null,
                     'published'           => $published,
                     'allow_comments'      => $allowComments,
                     'requires_moderation' => $requiresModeration,
                     'category'            => $category);

        $this->db->insert($this->tableName, $row);
    }
    // }}}
    // {{{ editEntry
    public function editEntry($id, $subject, $entryBody, $extendedEntry, $modifiedBy, $published, $allowComments, $requiresModeration, $category)
    {
        $entry = $this->getEntry($id);

        $row = Array('subject'             => $subject, 
                     'entry'               => $entryBody, 
                     'extended_entry'      => $extendedEntry,                     
                     'author'              => $entry['author'],
                     'create_date'         => $entry['create_date'],
                     'modify_date'         => time(),
                     'modified_by'         => $modifiedBy,
                     'published'           => $published,
                     'allow_comments'      => $allowComments,
                     'requires_moderation' => $requiresModeration,
                     'category'            => $category);
       
       $this->db->where('id', $id);
       $this->db->update($this->tableName, $row);
    }
    // }}}
    // {{{ deleteEntry
    public function deleteEntry($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ & getEntry
    public function & getEntry($id, $published = false)
    {
        $this->db->select('id, subject, entry, extended_entry, author, create_date, modify_date, modified_by, '.
                          'published, allow_comments, requires_moderation, category');

        if ($published) {
            $query = $this->db->getwhere($this->tableName, Array('id' => $id, 'published' => '1'));
        } else {
            $query = $this->db->getwhere($this->tableName, Array('id' => $id));
        }

        $retval = (array) $query->current();
        return $retval;
    }
    // }}}
    // {{{ hasEntry
    public function hasEntry($id, $published = false)
    {
        if ($published) {
            $result = $this->db->select('count(id) as count')->getwhere($this->tableName, Array('id' => $id, 'published' => '1'))->current();
        } else {
            $result = $this->db->select('count(id) as count')->getwhere($this->tableName, Array('id' => $id))->current();        
        }

        return (boolean)$result->count;
    }
    // }}}
    // {{{ getEntrySubject
    public function getEntrySubject($id)
    {
        $this->db->select('subject');
        $query = $this->db->getwhere($this->tableName, Array('id' => $id));

        return $query->current()->subject;
    }
    // }}}    
    // {{{ & getEntries
    public function & getEntries($published = false)
    {
        $this->db->select('id, subject, entry, extended_entry, author, create_date, modify_date, modified_by, '.
                          'published, allow_comments, requires_moderation, category');
        $this->db->orderby('create_date', 'desc');
        
        if ($published) {
            $query = $this->db->getwhere($this->tableName, Array('published' => '1'));
        } else {
            $query = $this->db->get($this->tableName);
        }

        $retval = $query->result(False);
        return $retval;
    }
    // }}}
    // {{{ & getCategories
    public function & getCategories()
    {
        $retval = Array();
        return $retval;
    }
    // }}}

    // {{{ List callbacks
    // {{{ getDate
    public function getDate($row)
    {
        return format::date($row['create_date']);
    }
    // }}}
    // {{{ getModifyDate
    public function getModifyDate($row)
    {
        return format::date($row['modify_date']);
    }
    // }}}
    // {{{ getModifiedBy
    public function getModifiedBy($row)
    {
        return format::date($row['modified_by']);    
    }
    // }}}
    // }}}
    // {{{ Options
    // {{{ getStatusOptions
    public function getStatusOptions()
    {
        return Array(0 => _("Draft"),
                     1 => _("Publish"));
    }
    // }}}
    // }}}
}

?>
