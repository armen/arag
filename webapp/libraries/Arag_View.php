<?php
/**
 *
 * @package    Arag
 * @author     Armen Baghumian
 * @since      Version 0.3
 * @filesource
 * $Id$
 */

/**
 * View Class
 *
 * @category    Libraries
 *
 */
class View extends View_Core {

    // {{{ Constructor
    public function __construct($name, $data = NULL, $type = NULL)
    {
        $type = empty($type) ? Config::item('smarty.templates_ext') : $type;

        if (!Kohana::find_file('views', $name, FALSE, $type)) {
            $type = Null;
        }
    
        parent::__construct($name, $data, $type);
    }
    // }}}
}

?>
