<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Based on:                                                               |
// |    PEAR DB Pager                                                        |
// |    Author:    Tomas V.V.Cox <cox@idecnet.com>                           |
// |    WebSite:   http://vulcanonet.com/soft/pager/                         |
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Pager
{
    // {{{ & getData
    /*
    * Gets all the data needed to paginate results
    * This is an associative array with the following
    * values filled in:
    *
    * array(
    *    'current' => X,    // current page you are
    *    'numrows' => X,    // total number of results
    *    'next'    => X,    // row number where next page starts
    *    'prev'    => X,    // row number where prev page starts
    *    'remain'  => X,    // number of results remaning *in next page*
    *    'numpages'=> X,    // total number of pages
    *    'from'    => X,    // the row to start fetching
    *    'to'      => X,    // the row to stop fetching
    *    'limit'   => X,    // how many results per page
    *    'maxpages'   => X, // how many pages to show (google style)
    *    'firstpage'  => X, // the row number of the first page
    *    'lastpage'   => X, // the row number where the last page starts
    *    'pages'   => array(    // assoc with page "number => start row"
    *                1 => X,
    *                2 => X,
    *                3 => X
    *                )
    *    );
    *
    * @param int $page    The page to start fetching
    * @param int $limit   How many results per page
    * @param int $numrows Number of results from query
    *
    * @return array associative array with data or DB_error on error
    *
    */
    
    public function & getData($page, $limit, $numrows, $maxpages = false)
    {
        if (empty($numrows) || ($numrows < 0)) {
            $result = Null;
            return $result;
        }

        // We cant accept page less and equal than 0
        $page  = ($page <= 0) ? 1 : $page;
        $limit = ($limit <= 0) ? $numrows : $limit;

        // Total number of pages
        $pages = ceil($numrows/$limit);
        $data['numpages'] = $pages;

        $page = (trim($page) == null)?1:$page;
        $page = ($page > $pages)?$pages:$page;
        $from = $limit * ($page - 1);

        // first & last page number
        // modified by Armen!
        $data['firstpage'] = 1;
        $data['lastpage']  = $pages;
        
        // Previous page
        $prevpage = $page - 1;
        $data['prevpage'] = ($prevpage >= 1)?$prevpage:null;

        // Next page
        $nextpage = $page + 1;
        $data['nextpage'] = ($nextpage <= $pages)?$nextpage:null;

        // Build pages array
        $data['pages'] = array();
        for ($i=1; $i <= $pages; $i++) {
            $offset = $limit * ($i-1);
            $data['pages'][$i] = $offset;
            // $from must point to one page
            if ($from == $offset) {
                // The current page we are
                $data['current'] = $i;
            }
        }
        /*
        if (!isset($data['current'])) {
            return PEAR::raiseError (null, 'wrong "from" param', null,
                                     null, null, 'DB_Error', true);
        }
        */

        // Limit number of pages (goole algoritm)
        if ($maxpages) {
            $radio = floor($maxpages/2);
            $minpage = $data['current'] - $radio;
            if ($minpage < 1) {
                $minpage = 1;
            }
            $maxpage = $data['current'] + $radio - 1;
            if ($maxpage > $data['numpages']) {
                $maxpage = $data['numpages'];
            }
            foreach (range($minpage, $maxpage) as $page) {
                $tmp[$page] = $data['pages'][$page];
            }
            $data['pages'] = $tmp;
            $data['maxpages'] = $maxpages;
        } else {
            $data['maxpages'] = null;
        }

        // Results remaining in next page & Last row to fetch
        if ($data['current'] == $pages) {
            $data['remain'] = 0;
            $data['to'] = $numrows;
        } else {
            if ($data['current'] == ($pages - 1)) {
                $data['remain'] = $numrows - ($limit*($pages-1));
            } else {
                $data['remain'] = $limit;
            }
            $data['to'] = $data['current'] * $limit;
        }
        $data['numrows'] = $numrows;
        $data['from']    = $from + 1;
        $data['limit']   = $limit;

        return $data;
    }
    // }}}
};

?>
