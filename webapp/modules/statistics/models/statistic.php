<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Statistic_Model extends Model
{
    const DAY    =  86400;
    const HOUR   =  3600;

    const TIMELINE = 'timeline';
    const PIE      = 'pie';
    const PLAIN    = 'plain';

    public function __construct()
    {
        parent::__construct();

        include_once(Arag::find_file('statistics', 'vendor/pChart', 'pChart', TRUE, 'class'));
        include_once(Arag::find_file('statistics', 'vendor/pChart', 'pData', TRUE, 'class'));
    }

    public function title()
    {
        
    }

    public function description()
    {

    }

    public function fetch($from, $to)
    {

    }

    
    public function series()
    {
        return array();
    }

    public function supports()
    {
        return array(self::PLAIN, self::TIMELINE, self::PIE);
    }

    public function timeline($data)
    {
        $pData = new pData;
        error_reporting(E_ALL & ~E_NOTICE); //HACK!

        foreach($this->series() as $serie_name => $serie_title) {
            $pData->AddSerie($serie_name);
            $pData->SetSerieName($serie_title, $serie_name);
        }

    
        foreach($data as $date => $values) {
            foreach($values as $serie_name => $value) {
                $pData->AddPoint($value, $serie_name, $date);
            }
        }
        $pData->AddAllSeries();
        $pData->SetXAxisFormat('date');

        $font = Kohana::Config('font');
        $font = $font['path'].'/'.$font['font'];

        $x = 1100;
        $y = 400;

        // Initialise the graph  
        $pChart = new pChart($x,$y);  
        $pChart->setFontProperties($font,8);
        $pChart->setDateFormat('D M y');

        $pChart->setGraphArea($x*(5/100),$y*(5/100),$x*(90/100),$y*(85/100));

        $pChart->drawScale($pData->GetData(),$pData->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,30,2);     
        $pChart->drawGrid(4,TRUE,230,230,230,50);  

        // Draw the cubic curve graph  
        $pChart->drawCubicCurve($pData->GetData(),$pData->GetDataDescription());  
        
        // Finish the graph    
        $pChart->drawLegend($x*(90/100),$y*(20/100),$pData->GetDataDescription(),255,255,255);  
        $pChart->setFontProperties($font,10);  
        $pChart->drawTitle($x*(10/100),$y*(5/100), $this->Title(),50,50,50,585);  

        return $pChart;
    }

    public function pie($data)
    {
        error_reporting(E_ALL & ~E_NOTICE); //HACK!

        $pData = new pData;

        foreach($data as $serie_name => $value) {
            $pData->AddPoint($value);
        }
        foreach($this->series() as $serie_title) {
            $pData->AddPoint($serie_title, 'Serie2');
        }
        $pData->AddAllSeries();  
        $pData->SetAbsciseLabelSerie('Serie2');


        $font = Kohana::Config('font');
        $font = $font['path'].'/'.$font['font'];

        // Initialise the graph  
        $Test = new pChart(380,200);  
        $Test->drawFilledRoundedRectangle(7,7,373,193,5,240,240,240);  
        $Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);  

        // Draw the pie chart  
        $Test->setFontProperties($font,8);  
        $Test->drawPieGraph($pData->GetData(),$pData->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);  
        $Test->drawPieLegend(310,15,$pData->GetData(),$pData->GetDataDescription(),250,250,250);  

        return $Test;
    }

    public function plain($data, $from, $to)
    {
        return New View('backend/plain', array('data' => $data, 'from' => $from, 'to' => $to));
    }
}
