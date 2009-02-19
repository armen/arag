<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

include_once('Services/Weather.php');

class Forecast_Model extends Model
{
    private $weather; //PEAR Service_Weather instance
    const METRIC = 'metric';

    public function __construct()
    {
        parent::__construct();
        $this->cache = New Cache;
    }

    public function weather()
    {
        if (!$this->weather) {
            $this->weather = &Services_Weather::service("WeatherDotCom");
            $this->weather->setUnitsFormat(self::METRIC);
            $partnerId  = Kohana::config('account.partnerId');
            $licenseKey = Kohana::config('account.licenseKey');
            $this->weather->setAccountData($partnerId, $licenseKey);
        }
        return $this->weather;
    }

    public function getLocationId($location)
    {
        $cached = $this->cache->get('location_id_'.$location);
        if ($cached) {
            return $cached;
        }

        $id = $this->weather()->searchLocation($location);
        if (is_array($id)) {
            $id = current(array_keys($id)); //it returned an array of results
        }
        $this->cache->set('location_id_'.$location, $id);

        return $id;
    }

    public function getLocation($location)
    {
        $id     = $this->getLocationId($location);
        $cached = $this->cache->get('location_name_'.$id);

        if ($cached) {
            return $cached;
        }

        $name = $this->weather()->getLocation($id);

        $this->cache->set('location_name_'.$id, $name);

        return $name;
    }

//     public function getForecast($location)
//     {
//         $location = $this->getLocationId($location);
//         return $this->weather()->getForecast($location);
//     }

    public function getWeather($location)
    {
        $id     = $this->getLocationId($location);
        $cached = $this->cache->get('location_weather_'.$id);

        if ($cached) {
            return $cached;
        }

        $weather                = $this->weather()->getWeather($id);
        $weather['temperature'] = (int)$weather['temperature'];
        $weather['wind']        = (int)$weather['wind'];
        $lifetime               = Kohana::config('cache.lifetime');

        $this->cache->set('location_weather_'.$id, $weather, Null, $lifetime);

        return $weather;
    }
}
?>
