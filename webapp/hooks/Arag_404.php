<?php  defined('SYSPATH') or die('No direct script access.');

Event::replace('system.404', array('Kohana', 'show_404'), 'arag_404');

function arag_404()
{
    url::redirect('core/frontend/messages/page_not_found');
}
