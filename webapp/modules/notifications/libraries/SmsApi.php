<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Maryam Talebi <mym.talebi@gmail.com>                            |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class SmsApi extends SoapClient
{
    // {{{ Constructor
    public function __construct($options = array())
    {
        ini_set("soap.wsdl_cache_enabled", 0);
        parent::__construct(Kohana::config('sms.wsdl'), $options);

        $this->config = Kohana::config('sms');
    }
    // }}}
    // {{{ __doRequest
    public function __doRequest($request, $location, $action, $version)
    {
	    Kohana::log('info', 'SmsApi request: ' . $request);
        return parent::__doRequest($request, $location, $action, $version);
    }
    // }}}
    // {{{ GetCredit
    public function get_credit()
    {
        $params = array('username' => $this->config['web_username'],
                        'password' => $this->config['web_password'],
                  );
        $this->GetCredit($params);
    }
    // }}}
    // {{{ SendSms
    public function send_sms($msg)
    {
        //var_dump(self::__getFunctions());exit;
        $params = array('username' => $this->config['web_username'],
                        'password' => $this->config['web_password'],
                        'from'     => $this->config['from'],
                        'to'       => $this->config['to'],
                        'text'     => $msg,
                        'isflash'  => $this->config['isflash'],
                        'udh'      => $this->config['udh'],
                  );
        $this->SendSms($params);
    }
    // }}}
    // {{{ ScheduleSms
    public function schedule_sms($msg)
    {
        $params = array('username'         => $this->config['web_username'],
                        'password'         => $this->config['web_password'],
                        'from'             => $this->config['from'],
                        'to'               => $this->config['to'],
                        'text'             => $msg,
                        'isflash'          => $this->config['isflash'],
                        'scheduleDateTime' => $this->config['scheduleDateTime'],
                  );
        $this->ScheduleSms($params);
    }
    // }}}
}
