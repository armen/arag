<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Download_Csv_Controller extends Controller
{
    // {{{ index_any
    public function index_any($namespace = Null)
    {
        Session::instance()->keep_flash('plist_'.$namespace);
        $resource = Session::instance()->get('plist_'.$namespace.'.resource', Array());
        $columns  = Session::instance()->get('plist_'.$namespace.'.columns', Array());

        $content = Null;
        $caption = Array();
        $csv     = tempnam(sys_get_temp_dir(), 'plist_csv');
        $handle  = fopen($csv, 'w');

        foreach ($columns as $column) {
            $caption[] = str_replace("\n", "", $column['label']);
        }

        fputcsv($handle, $caption);

        foreach ($resource as $row) {

            $csv_row = Array();

            foreach ($columns as $name => $column) {
                if ($column['virtual']) {
                    $virtual_content =  PList_Component::callCallback($name, $row);

                    if ($virtual_content != strip_tags($virtual_content)) {
                        // It has html tags, clean it up
                        $virtual_content = strip_tags($virtual_content);
                        $virtual_content = str_replace(Array("\n", '&nbsp;'), Array('', '-'), $virtual_content);
                    }
                    $csv_row[] = $virtual_content;
                } else {
                    $csv_row[] = $row[$name];
                }
            }

            fputcsv($handle, $csv_row);
        }

        fclose($handle);

        if (is_readable($csv)) {
            $content = file_get_contents($csv);
            @unlink($csv);
        }

        download::force(ucfirst($namespace).'_'.date('Y-m-d').'.csv', $content);
        exit;
    }
    // }}}
}
