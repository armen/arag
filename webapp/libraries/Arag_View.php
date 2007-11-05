<?php defined('SYSPATH') or die('No direct script access.');
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
    public function __construct($name, $data = NULL)
    {
        if (Config::item('smarty.integration') == True && 
            !preg_match('/\.(gif|jpe?g|png|tiff?|js|css|swf)$/Di', $name)) {
            
            $ext      = Config::item('smarty.templates_ext');
            $template = Kohana::find_file('views', $name.$ext, False, True);

            if ($template) {
                // There is a template file
                $this->kohana_filename = Kohana::find_file('views', $name.$ext, True, True);
                $this->kohana_filetype = $ext;
            
                // Preload data
                if (is_array($data) AND ! empty($data)) {
                    foreach($data as $key => $val) {
                        $this->data[$key] = $val;
                    }
                }

                Log::add('debug', 'View Class Initialized ['.str_replace(DOCROOT, '', $this->kohana_filename).']');

                return;
            }            
        } 
    
        // There is no smarty template file or smarty integration is disabled
        parent::__construct($name, $data);
    }
    // }}}
    // {{{ render
    /**
     * Render a view
     *
     * @access public
     * @param  string
     * @param  callback
     * @return mixed
     */
    public function render($print = False, $renderer = False)
    {
        if ($this->kohana_filetype === Config::item('smarty.templates_ext')) {

            // Load the view in the controller for access to $this
            $output = Kohana::instance()->smarty_include_view($this->kohana_filename, $this->data);

            // Pass the output through the user defined renderer
            if ($renderer == True && is_callable($renderer, True)) {
                $output = call_user_func($renderer, $output);
            }
            
            // Display the output
            if ($print == True) {
                print $output;
                return;
            }

        } else {

            $output = parent::render($print, $renderer);
        }

        // Output has been printed
        if ($print == True) {
            return;
        }

        // Output has not been printed, return it
        return $output;
    }
    // }}}
}

?>
