<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Server_Controller extends MessageQueue_Frontend
{
    // {{{ Properties

    public $continue       = True;
    public $messages_count = 0;
    public $log_level      = LOG_DEBUG;

    const MAX_MESSAGES_PER_LIFE = 10000;
    const SLEEP_TIMEOUT         = 10;

    // }}}
    // {{{ __construct
    public function __construct()
    {
        parent::__construct();

        pcntl_signal(SIGUSR1, array($this,  '_handle_kill'));
        pcntl_signal(SIGTERM, array($this,  '_handle_kill'));
        pcntl_signal(SIGINT,  array($this,  '_handle_kill'));
        pcntl_signal(SIGALRM, array($this,  '_handle_alarm'));
    }
    // }}}
    // {{{ __call
    public function __call($method, $arguments)
    {
        declare(ticks = 1);

        @list($keep_alive) = $arguments;
        $_keep_alive       = $keep_alive;
        $_in_progress_msgs = Array();
        $_pids             = Array();

        openlog('aragmq', LOG_ODELAY | LOG_PID, LOG_DAEMON);
        $this->_log("Starting up Aragmq...", LOG_INFO);

        while ($this->continue && ($this->messages_count < self::MAX_MESSAGES_PER_LIFE)) {

            if ((bool) $keep_alive == False) {
                // Do not keep this proccess alive, its usefull for cronjob based message queue.
                $this->continue = False;
            }

            $method   = ($method == 'index') ? 'common' : $method;
            $messages = $this->server_storage->getMessages($method);
            $count    = count($messages, COUNT_RECURSIVE);

            if ($count) {

                foreach ($messages as $message) {

                    if (!in_array($message->getId(), $_in_progress_msgs)) {

                        $this->_log("Got a message from '{$method}' channel.", LOG_DEBUG);
                        $this->messages_count++;
                        $_in_progress_msgs[$message->getId()] = $message->getId();

                        $params = @unserialize($message);
                        $pid    = pcntl_fork();

                        if ($pid == 0) {
                            // We are the child
                            if (isset($params['module']) && isset($params['model']) && isset($params['method'])) {

                                $arguments = isset($params['arguments']) && is_array($params['arguments']) ? $params['arguments'] : Array();
                                $model     = Model::load($params['model'], $params['module']);
                                $cpid      = posix_getpid();

                                $this->_log("Calling({$cpid})... '{$params['model']}::{$params['method']}' from '{$params['module']}' module.", LOG_DEBUG);

                                $result = call_user_func_array(array($model, $params['method']), $arguments);

                                if ($result) {
                                    $this->_log("Set it as processed.", LOG_DEBUG);
                                    $this->server_storage->setProcessed($message);
                                    exit(0);
                                } else {
                                    $this->_log("It's been failed. moved it in to the end of queue.", LOG_DEBUG);
                                    exit(-1);
                                }

                            } else {
                                $this->_log("Got invalid message and removed it from queue.", LOG_ERR);
                                Kohana::log('error', "Dropr message is not a valid message, it should be contain at ".
                                                     "least three paramaters (module, model, method). received message is:\n".
                                                     var_export($message, True));
                                $this->server_storage->setProcessed($message);
                                exit(-2);
                            }

                        } else if ($pid > 0) {
                            Database::instance('default');
                            Session::instance();
                            Kohana::log_save();
                            $_pids[$pid] = $message->getId();
                        }
                    }
                }

            } else {
                $this->_log("Nothing to do. going to sleep.", LOG_DEBUG);
                sleep(self::SLEEP_TIMEOUT);
                $this->_log("Woke up from sleep - checking for messages ...", LOG_DEBUG);
            }

            if (($pid = pcntl_waitpid(0, $status, WNOHANG)) > 0) {
                $this->_log("'{$pid}' returned with '".pcntl_wexitstatus($status)."' code.", LOG_DEBUG);
                $messageId = $_pids[$pid];
                unset($_in_progress_msgs[$messageId]);
                unset($_pids[$pid]);
                $this->_log(var_export($_pids, true), LOG_DEBUG);
                $this->_log(var_export($_in_progress_msgs, true), LOG_DEBUG);
            }
        }

        if ($this->continue) {
            $this->_log("Restarting after sending ".$this->messages_count." messages into the world.", LOG_INFO);
        } else {
            $this->_log("Cleaning up and terminating on request. Goodbye.", LOG_INFO);
        }
    }
    // }}}
    // {{{ _log
    public function _log($message, $level)
    {
        if ($level <= $this->log_level) {
            syslog($level, $message);
        }
    }
    // }}}
    // {{{ _handle_kill
    public function _handle_kill($signal)
    {
        // Kill it
        $this->continue = False;
    }
    // }}}
    // {{{ _handle_alarm
    public function _handle_alarm($signal)
    {
        return;
    }
    // }}}
}
