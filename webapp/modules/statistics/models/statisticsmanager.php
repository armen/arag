<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class StatisticsManager_Model extends Model
{
    public function getPlugins()
    {
        $plugins = Kohana::Config('plugins');

        foreach($plugins as $plugin_name => $module) {
            $plugin                = Model::load($plugin_name.'_statistics', $module);
            $plugins[$plugin_name] = $plugin;
        }

        return $plugins;
    }

    public function getBeginning()
    {
        $this->db->select('create_date')->from('user_users')->orderby('create_date')->limit(1);
        return (int)$this->db->get()->current()->create_date;
    }
}
