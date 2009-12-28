<?php

Event::replace('system.404', array('Kohana', 'show_404'), 'arag_404');

function arag_404()
{
    Kohana::log('info', 'Following controller does not exists: `'.url::current().'`');
    (PHP_SAPI != 'cli') AND url::redirect(ltrim(Router::$content_type.'/page_not_found', '/'));
    exit;
}
