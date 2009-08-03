<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Messengers_Model extends Model
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();
    }
    // }}}
    // {{{ getMessengers
    public function getMessengers()
    {
        $messengers = Array();
        $settings   = Arag_Config::get('messenger_status_settings_add', array());
        $details    = Kohana::config('messengers.details');

        foreach ($settings['id'] as $idx => $id) {

            $type = $settings['type'][$idx];
            $id   = $details[$type]['stript_domain'] ? preg_replace('/@.*$/', '', $id) : $id;
            $href = str_replace('%messenger_id%', $id, $details[$type]['href']);

            if ($details[$type]['check_status_server_side']) {

                $online_image_url  = str_replace('%messenger_id%', $id, $details[$type]['online_image_url']);
                $offline_image_url = str_replace('%messenger_id%', $id, $details[$type]['offline_image_url']);
                $status_url        = str_replace('%messenger_id%', $id, $details[$type]['status_url']);

                $online                    = strpos(file_get_contents($status_url), $details[$type]['offline_status_msg']) === False;
                $messengers[$idx]['image'] = ($online) ? $online_image_url : $offline_image_url;

            } else {
                $messengers[$idx]['image'] = str_replace('%messenger_id%', $id, $details[$type]['status_image_url']);
            }

            $messengers[$idx]['href']    = $href;
            $messengers[$idx]['subject'] = $settings['subject'][$idx];
        }

        return $messengers;
    }
    // }}}
}
