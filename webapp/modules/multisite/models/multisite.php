<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// |         Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class MultiSite_Model extends Model
{
    // {{{ Properties

    private $tableNameMultiSite;
    private $tableNameApps;
    private $tableNameGroups;
    private $tableNameUsers;
    private $tableNameDatabases;

    const BLOCK     = 2;
    const URI       = 4;
    const BLOCK_URI = 6;
    // }}}
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Set the table name
        $this->tableNameMultiSite = 'multisite_databases';
        $this->tableNameUsers     = 'user_users';
        $this->tableNameGroups    = 'user_groups';
        $this->tableNameApps      = 'user_applications';
        $this->tableNameDatabases = 'multisite_databases';
    }
    // }}}
    // {{{ getApps
    public function getApps($name, $databaseID)
    {
        $this->db->select('default_group, create_date, database_id');
        $this->db->select($this->tableNameApps.".name AS app_name");
        $this->db->select($this->tableNameDatabases.".name as db_name");
        $this->db->from($this->tableNameApps);
        $this->db->join($this->tableNameDatabases, $this->tableNameApps.'.database_id', $this->tableNameDatabases.'.id');

        if ($databaseID) {
            $this->db->where("database_id", $databaseID);
        }

        if ($name != "") {
            $this->db->like($this->tableNameApps.".name", $name);
        }

        $retval = $this->db->orderby('app_name', 'ASC')->get()->result(False);
        return $retval;
    }
    // }}}
    // {{{ getDate
    public function getDate($row)
    {
        return format::date($row['create_date']);
    }
    // }}}
    // {{{ getIDs
    public function getIDs()
    {
        $this->db->select('DISTINCT '.$this->tableNameApps.'.database_id');
        $this->db->select($this->tableNameDatabases.".name");
        $this->db->from($this->tableNameApps);
        $this->db->join($this->tableNameDatabases, 'database_id', $this->tableNameDatabases.'.id');

        $retval = $this->db->get()->result_array(false);

        return $retval;
    }
    // }}}
    // {{{ getModules
    public function getModules($excludeModules = Array())
    {
        $modules = Model::load('Modules', 'core');
        return $modules->getModules($excludeModules);
    }
    // }}}
    // {{{ generateVerifyUri
    public function generateVerifyUri($length)
    {
        return sha1((string)time().text::random('alnum', $length));
    }
    // }}}
    // {{{ sendEmail
    public function sendEmail($recipient, $strings, $settings)
    {
        foreach ($strings as $str => $replace) {
            $settings['template'] = str_replace("%$str%", $replace, $settings['template']);
        }

        require_once Kohana::find_file('vendor', 'swift/Swift');
        require_once Kohana::find_file('vendor', 'swift/Swift/Connection/SMTP');

        $conn =& new Swift_Connection_SMTP($settings['smtpserver'], $settings['smtpport']);

        if ($settings['authentication']) {
            require_once Kohana::find_file('vendor', 'swift/Swift/Authenticator/LOGIN');
            $conn->attachAuthenticator(new Swift_Authenticator_LOGIN());
            $conn->setUsername($settings['username']);
            $conn->setPassword($settings['password']);
        }

        // Start Swift
        $swift =& new Swift($conn);

        // Create the message
        $message =& new Swift_Message($settings['subject'], nl2br($settings['template']), "text/html");

        return $swift->send($message, $recipient, $settings['sender']);

    }
    // }}}
    // {{{ getNextDB
    public function getNextDB(&$DSN, &$databaseId)
    {
        $DSN        = Kohana::config('database.default.connection');
        $databaseId = 1;

        return True;
    }
    // }}}
    // {{{ getAppUrl
    public function getAppUrl($appname)
    {
        return str_replace('arag', $appname, url::base());
    }
    // }}}
}
?>
