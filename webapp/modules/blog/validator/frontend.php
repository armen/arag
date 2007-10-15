<?php

/*
 * Validation of View method
 */

$validator['view']['fields'] = Array(1 => 'Id');
$validator['view']['rules']  = Array(1 => 'required|numeric|callback__check_entry');

?>
