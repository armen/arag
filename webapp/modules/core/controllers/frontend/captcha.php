<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Captcha_Controller extends Controller
{
    // {{{ __call
	public function __call($method, $args)
	{
		Captcha::factory($this->uri->segment(5))->render(FALSE);
	}
    // }}}
}
