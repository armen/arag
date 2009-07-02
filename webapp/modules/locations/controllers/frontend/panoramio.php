<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Panoramio_Controller extends Controller
{
    // {{{ search_read
    public function search_read($x, $y) //Its a proxy to face cross-domain scripting issue.
    {
        $cache  = New Cache;
        $id     = 'panoramio_'.$x.'_'.$y;
        $cached = $cache->get($id);
        if (!$cached) {
            $cached = file_get_contents('http://www.panoramio.com/map/get_panoramas.php?order=popularity&set=public&from=0&to=10&minx='.($x-1).'&miny='.($y-1).'&maxx='.($x+1).'&maxy='.($y+1).'&size=small');
            $cache->set($id, $cached);
        }
        print $cached;
        die();
    }
    // }}}
}
