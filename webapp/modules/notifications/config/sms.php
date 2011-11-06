<?php
$config = array(
    'sms_channel' => 'sms_webservice', // kannel or sms_webservice

    // kannel settings
    'url'      => 'http://192.168.0.8:13013/cgi-bin/sendsms?username=%s&password=%s&to=+%s&text=%s',
    'username' => 'tester',
    'password' => 'foobar',
    'prefix'   => '+98',

    //webservice settings
    'wsdl'             => 'http://smsonline.ir/post/send.asmx?WSDL',
    'web_username'     => '',
    'web_password'     => '',
    'from'             => '',
    'to'               => array('9396837884','9376187620'),
    'isflash'          => false,
    'udh'              => '',
    'scheduleDateTime' => '',
    'period'           => '', // Once or Daily or Weekly or Monthly or Yearly or Custom
);
?>
