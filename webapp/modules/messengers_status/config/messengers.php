<?php

$config = Array
(
    'types' => Array
    (
        'yahoo' => "Yahoo Messenger"
    ),
    'details' => Array
    (
        'yahoo' => Array
        (
            'check_status_server_side' => False,
            'status_url'               => 'http://opi.yahoo.com/online?u=%messenger_id%&m=',
            'offline_status_msg'       => 'NOT ONLINE',
            'online_image_url'         => Null,
            'offline_image_url'        => Null,
            'status_image_url'         => 'http://opi.yahoo.com/online?u=%messenger_id%&m=g&t=1',
            'stript_domain'            => True,
            'href'                     => 'ymsgr:sendIM?%messenger_id%',
        )
    )
);
