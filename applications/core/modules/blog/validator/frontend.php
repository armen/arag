<?php

/*
 * Validation of View method
 */

$validator['view']['rules'] = Array(Array(_("ID"), 'required|numeric|callback__check_entry'));

?>
