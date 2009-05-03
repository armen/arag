<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Peyman Karimi <zeegco@yahoo.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        // Load the models
        $this->theme_manager = new Thememanager_Model;
    }
    // }}}
    // {{{ get_style_any
    function get_style_any()
    {
        $cache          = Cache::instance();
        $dump           = $cache->get(APPNAME.'_styles');

        if (!$dump) {
            $default_styles = $this->theme_manager->getDefaults();
            $styles         = $this->theme_manager->get();
            $dump           = '';

            foreach($styles as $style => $value) {
                    unset($styles[$style]);
                    if (isset($default_styles[$style])) {
                        $styles[$style]          = $default_styles[$style];
                        $styles[$style]['value'] = $value;
                    }
            }
            foreach($styles as $style) {
                    if ($style['type'] == 'file') {
                        $dump .= $style['selector'] . '{' . $style['property'] . ":url(". url::base(). 'modpub/theme_manager/uploaded/' . $style['value'] . ");}";
                    } else {
                        $dump .= $style['selector'] . '{' . $style['property'] . ':' . $style['value'] . ';}';
                    }
            }
            $cache->set(APPNAME.'_styles', $dump);
        }

        header("Content-type: text/css");
        echo $dump;
    }
    // }}}
}
