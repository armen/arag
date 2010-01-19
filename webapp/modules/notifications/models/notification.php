<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Notification_Model extends Model
{
    const SMS   = 'sms';
    const EMAIL = 'email';

    private $event;

    public function __construct( $message = Null )
    {
        $this->setMessage($message);
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function types()
    {
        return Array(self::SMS => _("SMS"), self::EMAIL => _("E-Mail"));
    }

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

    public function sms($user)
    {
        $config   = Kohana::config('sms');

        $profiles = Model::load('UserProfile', 'user_profile');
        $profile  = $profiles->getProfile($user['username']);

        // Remove first 0 from cellphone number and attach it to prefix.
        // This way, for example 09124444444 from profile becomes +989124444444.
        $cell     = $profile['cellphone'];
        $cell     = $config['prefix'].substr($cell, 1);

        $url      = sprintf($config['url'], $config['username'], $config['password'], $cell, urlencode($this->getMessage()));

        return (boolean) @file_get_contents($url);
    }

    public function email()
    {

    }
}
