<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type: modifier
 * Name: filter_amount
 * File: modifier.filter_amount.php
 * Purpose: Filter amount
 * Input: string: comma separated amouont
 * Example: {$value|filter_amount}
 * Author: Sasan Rose <sasan.rose {at} gmail.com>
 * Date: 2010-06-24 22:19:02
 * Modfied on: 24 Jun 2010
 */

function smarty_modifier_filter_amount($string)
{
    return format::filter_amount($string);
}

/* vim: set expandtab: */
