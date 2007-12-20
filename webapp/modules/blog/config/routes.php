<?php defined('SYSPATH') or die('No direct script access.');

$config = array
(
    '([a-zA-Z]{2}/)?blog'            => 'blog/frontend',
    '([a-zA-Z]{2}/)?blog/backend'    => 'blog/backend/entry',
    '([a-zA-Z]{2}/)?blog/admin'      => 'blog/backend/entry',
    '([a-zA-Z]{2}/)?blog/post'       => 'blog/backend/entry/post',
    '([a-zA-Z]{2}/)?blog/entries'    => 'blog/backend/entry',
    '([a-zA-Z]{2}/)?blog/categories' => 'blog/backend/categories'
);

?>
