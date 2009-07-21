<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Search_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        // Do not call parent constructor
    }
    // }}}
    // {{{ getByParent
    public function getByParent($id = 0)
    {
        $locations = Model::load('Locations', 'locations');
        print json_encode($locations->getByParent($id));
    }
    // }}}
    // {{{ getSiblings
    public function getSiblings($id = 1)
    {
        $locations = Model::load('Locations', 'locations');
        print json_encode($locations->getSiblings($id));
    }
    // }}}
    public function convert_any()
    {
        Model::load('Locations', 'locations')->convert();
    }
}
