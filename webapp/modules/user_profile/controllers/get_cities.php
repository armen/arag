<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Get_Cities_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        $this->layout = 'get_cities';

        parent::__construct();
    }
    // }}}
    // {{{ get_cities_of
    public function get_cities_of($province)
    {
        // Load the model
        $profile_man = new UserProfile_Model;

        $this->layout->content         = new View('cities');
        $this->layout->content->cities = $profile_man->getCities($province);
    }
    // }}}
}

?>
