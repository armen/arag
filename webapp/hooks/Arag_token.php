<?php

if (Config::item('token.enable')) {
    Event::add('system.ready', 'arag_token');
}

function arag_token()
{
    Token::validate();
    Token::gc();
}
