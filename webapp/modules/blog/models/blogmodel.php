<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class BlogModel extends Model 
{
    // {{{ Properties
    
    var $tableName;

    const PROP_PUBLISH             = 1;
    const PROP_ALLOW_COMMENTS      = 2;
    const PROP_REQUIRES_MODERATION = 4;

    // }}}
    // {{{ Constructor
    function BlogModel()
    {
        parent::Model();

        // Connecting to the database
        $this->load->database();

        // Set the table name
        $this->tableName = 'blog_entries';
    }
    // }}}
    // {{{ createEntry
    function createEntry($subject, $entry, $extendedEntry, $author, $published, $allowComments, $requiresModeration, $category)
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
    function editEntry($id, $subject, $entryBody, $extendedEntry, $modifiedBy, $published, $allowComments, $requiresModeration, $category)
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
    function deleteEntry($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ & getEntry
    function & getEntry($id, $published = false)
    {
        $this->db->select('id, subject, entry, extended_entry, author, create_date, modify_date, modified_by, '.
                          'published, allow_comments, requires_moderation, category');

        if ($published) {
            $query = $this->db->getwhere($this->tableName, Array('id' => $id, 'published' => '1'));
        } else {
            $query = $this->db->getwhere($this->tableName, Array('id' => $id));
        }

        $retval = $query->first_row('array');
        return $retval;
    }
    // }}}
    // {{{ hasEntry
    function hasEntry($id)
    {
        $this->db->select('id');
        $query = $this->db->getwhere($this->tableName, Array('id' => $id));

        return (boolean)$query->num_rows();
    }
    // }}}
    // {{{ getEntrySubject
    function getEntrySubject($id)
    {
        $this->db->select('subject');
        $query = $this->db->getwhere($this->tableName, Array('id' => $id));

        return $query->row()->subject;
    }
    // }}}    
    // {{{ & getEntries
    function & getEntries($published = false)
    {
        $this->db->select('id, subject, entry, extended_entry, author, create_date, modify_date, modified_by, '.
                          'published, allow_comments, requires_moderation, category');
        $this->db->orderby('create_date', 'desc');
        
        if ($published) {
            $query = $this->db->getwhere($this->tableName, Array('published' => '1'));
        } else {
            $query = $this->db->get($this->tableName);
        }

        $retval = $query->result_array();
        return $retval;
    }
    // }}}
    // {{{ & getCategories
    function & getCategories()
    {
        $retval = Array();
        return $retval;
    }
    // }}}

    // {{{ List callbacks
    // {{{ getDate
    function getDate($row)
    {
        return ($row['create_date']) ? date('Y-m-d H:i:s', $row['create_date']) : '-';        
    }
    // }}}
    // {{{ getModifyDate
    function getModifyDate($row)
    {
        return ($row['modify_date']) ? date('Y-m-d H:i:s', $row['modify_date']) : '-';
    }
    // }}}
    // {{{ getModifiedBy
    function getModifiedBy($row)
    {
        return ($row['modified_by']) ? $row['modified_by'] : '-';
    }
    // }}}
    // }}}
    // {{{ Options
    // {{{ getStatusOptions
    function getStatusOptions()
    {
        return Array(0 => _("Draft"),
                     1 => _("Publish"));
    }
    // }}}
    // }}}
}

?>
