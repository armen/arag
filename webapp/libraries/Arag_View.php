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
        $type = empty($type) ? Kohana::config('smarty.templates_ext') : $type;

        if (!Kohana::find_file('views', $name, FALSE, $type) && !file::is_path_absolute($name)) {
            $type = Null;
        }

        parent::__construct($name, $data, $type);
    }
    // }}}
    // {{{ __toString
    public function __toString()
    {
        try {
            return parent::__toString();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    // }}}
    // {{{ toArray
    public function toArray()
    {
        return array_merge(parent::$kohana_global_data, $this->kohana_local_data);
    }
    // }}}
    // {{{ set_filename
	public function set_filename($name, $type = NULL)
	{
        if (file::is_path_absolute($name)) {
            $this->kohana_filename = $name;

            // Use the specified type
			($this->kohana_filetype == NULL && isset($type)) AND $this->kohana_filetype = $type;

            return $this;
        }

        return parent::set_filename($name, $type);
    }
    // }}}
}

?>
