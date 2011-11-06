<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Notification_Model extends Model
{
    // {{{ Properties
    const SMS       = 'sms';
    const EMAIL     = 'email';

    // ALL is a constant used for all types ,statuses and channels of notifications to include all types, all channles, and all statuses in each case.
    const ALL       = 'all';

    // defaults for status of notification
    const UNVISITED = 1;
    const VISITED   = 0;

    // defults fo type of notification
    const NOTIFICATION_DEFAULT   = 'notification_default';

    // defaults for channel of notificatin
    const MOBILE    = 'mobile';
    const WEb       = 'web';
    const IVR       = 'ivr';

    private $event;
    private $tableName = 'notifications';
    // }}}

    // {{{ __construct
    public function __construct( $message = Null )
    {
        parent::__construct();

        $this->setMessage($message);
        //$this->tableName = 'notifications';
    }
    // }}}
    // {{{ setMessage
    public function setMessage($message)
    {
        $this->message = $message;
    }
    // }}}
    // {{{ getMessage
    public function getMessage()
    {
        return $this->message;
    }
    // }}}
    // {{{ types
    public function types()
    {
        return Array(self::SMS => _("SMS"), self::EMAIL => _("E-Mail"));
    }
    // }}}
    // {{{ notify
    public function notify($user, $types = Null)
    {
        if (!$types) {
            $types = $this->types();
        }

        $users = Model::load('Users', 'user');
        $user = $users->getUser($user);

        foreach($types as $type => $title) {
            $this->$type($user);
        }
    }
    // }}}
    // {{{ sms
    public function sms($to = "")
    {
        $config   = Kohana::config('sms');

        if($config['sms_channel'] == 'kannel') {
            if(is_numeric($to)) {
                $cell     = $to;
            } elseif(is_string($to)) {
                $profiles = Model::load('UserProfile', 'user_profile');
                $profile  = $profiles->getProfile($to);

                // Remove first 0 from cellphone number and attach it to prefix.
                // This way, for example 09124444444 from profile becomes +989124444444.
                $cell     = $profile['cellphone'];
            } else {
                return false;
            }

            $cell = $config['prefix'].substr($cell, 1);
            $url  = sprintf($config['url'], $config['username'], $config['password'], $cell, urlencode($this->getMessage()));

            return (boolean) @file_get_contents($url);

        } elseif($config['sms_channel'] == 'sms_webservice') {
            $soapClient = new SmsApi();
            $soapClient->send_sms($this->message);
        }
    }
    // }}}
    // {{{ email
    public function email($recipients, $subject)
    {
        $email = Model::load('MultiSite', 'multisite');

        $settings['template'] = $this->getMessage();
        $settings['subject']  = $subject;

        return $email->sendEmail($recipients, array(), $settings);
    }
    // }}}
    // {{{ add
    public function add($description, $user, $channel = self::WEB, $uri, $type = NOTIFICATION_DEFAULT, $title = NULL, $icon = NULL)
    {
        $notification = array(
            'title'       => $title,
            'description' => $description,
            'user'        => $user,
            'icon'        => $icon,
            'uri'         => $uri,
            'Channel'     => $channel,
            'type'        => $type,
            'created_by'  => $username = Session::instance()->get('user.username'),
            'create_date' => time(),
            'visited'     => 0
        );
        $this->db->insert($this->tableName, $notification);
    }
    // }}}
    // {{{ show
    public function show($username, $visited = self::UNVISITED)
    {
        // show all unvisited notifications of a user. Parameter $flag can accept 3 values: ALL to show all notifications, VISITED to show visited notifications
        // and UNVISITED to show unvisited ones.

        $this->db->select('id, title, description, icon, uri, channel, type, created_by, create_date, visited')
            ->from($this->tableName)
            ->where('user', $username)
            ->orderby('create_date', 'DESC');

        if($visited != self::ALL) {
            $this->db->where('visited', $visited);
        }

        $notifications = $this->db->get()->result_array(False);

        return $notifications;
    }
    // }}}
    // {{{ countNotifications
    public function countNotifications($user, $type = self::ALL, $visited = self::UNVISITED)
    {
        $this->db->select('count(id) as count');
        $query  = $this->db->where( 'user', $user );

        if($type != self::ALL ) {
            $query = $this->db->where( 'type', $type );
        }

        if($visited != self::ALL ) {
            $query = $this->db->where( 'visited', $visited );
        }

        $query  = $this->db->get($this->tableName);
        $result = $query->current()->count;
        return  $result;
    }
    // }}}
    // {{{ delete
    public function delete($id)
    {
        $this->db->delete($this->tableName, Array('id' => $id));
    }
    // }}}
    // {{{ setVisited
    public function setVisited($id)
    {
        $this->db->where('id', $id);
        $row = array('visited' => self::VISITED);
        $this->db->update($this->tableName, $row);
    }
    // }}}
}
