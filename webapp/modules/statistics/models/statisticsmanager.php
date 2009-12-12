<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class StatisticsManager_Model extends Model
{
    public function getPlugins($feature = Null)
    {
        $plugins_list = Kohana::Config('plugins');
        $plugins      = Array();
        foreach($plugins_list as $plugin_name => $module) {
            $plugin                = Model::load($plugin_name.'_statistics', $module);
            if ($feature && !in_array($feature, $plugin->supports())) {
                continue;
            }
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
