<?php

$config = array
(
    'channels' => Array
    (
        MessageQueue_Model::COMMON_CHANNEL  => 'frontend/server/common',
        MessageQueue_Model::DAILY_CHANNEL   => 'frontend/server/daily',
        MessageQueue_Model::HOURLY_CHANNEL  => 'frontend/server/hourly',
        MessageQueue_Model::MONTHLY_CHANNEL => 'frontend/server/monthly',
        MessageQueue_Model::WEEKLY_CHANNEL  => 'frontend/server/weekly'
    ),
    'server_storage_type' => 'filesystem',
    'server_storage_dsn'  => MODPATH.'message_queue/queue',
    'client_storage_type' => 'filesystem',
    'client_storage_dsn'  => '/var/spool/dropr/client'
);
