<?php
/**
 * Arag
 *
 * @package      Arag
 * @author       Armen Baghumian
 * @since        Version 0.3
 * $Id$
 */

// ------------------------------------------------------------------------

/**
 * text Class
 *
 * @category    Helper
 *
 */

class valid extends valid_Core {
    
    // {{{ id
    public static function id($id, $utf8 = FALSE)
    {
		return ($utf8 === TRUE)
			? (bool) preg_match('/^[\pL][\pL\pN_]++$/uD', (string) $id)
			: (bool) preg_match('/^[a-z][a-z0-9_]++$/iD', (string) $id);    
    }
    // }}}

} // End text
