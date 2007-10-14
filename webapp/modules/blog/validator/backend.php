<?php

$validator['post']['write']['rules']          = Array ('subject'    => 'trim|required|min_length[5]|max_length[12]|xss_clean');
$validator['post']['write']['error_messages'] = Array ('required'   => '%s is required.', 
                                                       'min_length' => 'minimum length of %s is %d');

?>
