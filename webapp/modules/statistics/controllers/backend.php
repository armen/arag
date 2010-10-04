<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    const DAILY   = 'daily';
    const OVERALL = 'overall';
    const TODAY   = 'today';

    public function __construct()
    {
        parent::__construct();

        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Statistics"));
        $this->global_tabs->addItem(_("Statistics"), 'statistics/backend');
        $this->global_tabs->addItem(_("Plain"), 'statistics/backend/index', 'statistics/backend');
        $this->global_tabs->addItem(_("Overall"), 'statistics/backend/timeline/'.self::OVERALL, 'statistics/backend');
        $this->global_tabs->addItem(_("Daily"), 'statistics/backend/timeline/'.self::DAILY, 'statistics/backend');

        $this->statistics = Model::load('StatisticsManager', 'statistics');
    }

    public function index_any()
    {
        $from = date::get_time('from');
        $to   = date::get_time('to');

        if (!$from) {
            $from = mktime(0,0,0);
        }

        if (!$to) {
            $to = mktime(23,59,59);
        } else {
            $to += 86399;
        }

        $plugins = $this->statistics->getPlugins(Statistic_Model::PLAIN);
        foreach($plugins as $plugin_name => &$plugin) {
            $data         = $plugin->fetch($from, $to);
            $plugin->data = $data;
        }
        $this->layout->content = New View('backend/plain_result');
        $this->layout->content->plugins = $plugins;
        $this->layout->content->from = $from;
        $this->layout->content->to = $to;
    }

    public function pie_read()
    {
        $plugins = $this->statistics->getPlugins(Statistic_Model::PIE);
        set_time_limit(0);
        foreach($plugins as $plugin_name => &$plugin) {
            $data    = Array();
            $from    = mktime(0, 0, 0);

            $to      = time();
            $data = $plugin->fetch($from, $to);

            $chart = $plugin->pie($data);

            $filename = 'modpub/statistics/cache/'.$plugin_name.'_'.$from.'_'.$time.'.png';
            $chart->Render(DOCROOT.'/'.$filename);
            $plugin->image = $filename;
        }

        $this->layout->content = New View('backend/statistics');
        $this->layout->content->plugins = $plugins;
    }

    public function timeline_read($timeline = self::DAY)
    {
        $plugins = $this->statistics->getPlugins(Statistic_Model::TIMELINE);
        set_time_limit(0);
        foreach($plugins as $plugin_name => &$plugin) {
            $data    = Array();
            $from    = $this->statistics->getBeginning();
            $to      = time();
            $time    = $from;

            while($time <= $to) {
                $time        = $time+$plugin->interval();

                if ($timeline == self::DAILY) {
                    $data[$time] = $plugin->fetch($time, $time+$plugin->interval());
                } else if ($timeline == self::OVERALL) {
                    $data[$time] = $plugin->fetch($from, $time);
                }
            }

            $chart = $plugin->timeline($data);

            $filename = 'modpub/statistics/cache/'.$plugin_name.'_'.$from.'_'.$time.'_'.$timeline.'.png';
            $chart->Render(DOCROOT.'/'.$filename);
            $plugin->image = $filename;
        }

        $this->layout->content = New View('backend/statistics');
        $this->layout->content->plugins = $plugins;
    }
}
