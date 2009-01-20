<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Peyman Karimi <zeegco@yahoo.com>                               |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Load the models
        $this->locations = new Locations_Model;

        // Default page title
        $this->layout->page_title = _("Locations");

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Locations"));
        $this->global_tabs->addItem(_("Locations"), 'locations/backend');
        $this->global_tabs->addItem(_("Settings"), 'locations/backend/settings');

        // Validation Messages
        $this->validation->message('numeric', _("%s should be numeric"));
        $this->validation->message('required', _("%s is required"));
        $this->validation->message('alpha_numeric', _("%s must be alph-numeric."));
    }
    // }}}
    // {{{ Countries
    // {{{ list_countries_any
    public function list_countries_any()
    {
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $data = array('flagsaved' => $this->session->get_once('locations_country_saved'));
        $countries = new PList_Component('countries');
        $countries->setResource($this->locations->getCountries());
        $countries->setLimit(Arag_Config::get('limit', 0));
        $countries->addColumn('id', _("ID"), PList_Component::HIDDEN_COLUMN);
        $countries->addColumn('country', _("Name"));
        $countries->addAction('locations/backend/edit_country/#id#', _("Edit"), 'edit_action');
        $countries->addAction('locations/backend/delete_country/#id#', _("Delete"), 'delete_action');
        $countries->addAction('locations/backend/list_provinces/#id#', _("View province of this country"), 'view_action');

        $this->layout->content = new View('backend/list_countries', $data);
    }
    // }}}
    // {{{ add_country
    // {{{ add_country_read
    public function add_country_read()
    {
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->layout->content = new View('backend/add_country');
    }
    // }}}
    // {{{ add_country_write
    public function add_country_write()
    {
        $country = $this->input->post('country', Null, true);
        $tempCountry = $this->locations->isCountryDefined($country);
        if (!$tempCountry) {
            $this->locations->createCountry($country);
        }
        $this->session->set('locations_country_saved', true);
        url::redirect('locations/backend/list_countries');
    }
    // }}}
    // {{{ add_country_validate_write
    public function add_country_validate_write()
    {
        $this->validation->name('country', _("Country Name"))
             ->pre_filter('trim', 'country')
             ->add_rules('country', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_country_write_error
    public function add_country_write_error()
    {
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $data = array ('country' => $this->input->post('country', Null, true));

        $this->layout->content = new View('backend/add_country', $data);
    }
    // }}}
    // }}}
    // {{{ edit_country
    // {{{ edit_country_read
    function edit_country_read($id = 0)
    {
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(_("Edit Country"), 'locations/backend/edit_country/%country_id%',
                                    'locations/backend');
        $this->global_tabs->setParameter('country_id', $id);
        $this->layout->content = new View('backend/add_country', (array) $this->locations->getCountry($id));
    }
    // }}}
    // {{{ edit_country_validate_read
    public function edit_country_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_country_read_error
    public function edit_country_read_error()
    {
        $this->_invalid_request(Null, _("ID is required"));
    }
    // }}}
    // {{{ edit_country_write
    function edit_country_write()
    {
        $id         = $this->input->post('id', Null, true);
        $country    = $this->input->post('country', Null, true);

        $tempCountry = $this->locations->isCountryDefined($country);
        if (!($tempCountry and ($tempCountry['id'] != $id))) {
            $this->locations->editCountry($id, $country);
        }

        $this->session->set('locations_country_saved', true);
        url::redirect('locations/backend/list_countries');
    }
    // }}}
    // {{{ edit_country_validate_write
    function edit_country_validate_write()
    {
        $this->validation->name('id', _("ID"))
             ->add_rules('id', 'valid::numeric')
             ->add_rules('id', 'required');

        $this->validation->name('country', _("Country Name"))
             ->pre_filter('trim', 'country')
             ->add_rules('country', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_country_write_error
    function edit_country_write_error()
    {
        $data = array ('id'         => $this->input->post('id', Null, true),
                       'country'    => $this->input->post('country', Null, true),
                      );

        $this->layout->content = new View('backend/add_country', $data);
    }
    // }}}
    // }}}
    // {{{ delete_country
    // {{{ delete_country_read
    public function delete_country_read($country_id)
    {
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(_("Edit Country"), 'locations/backend/edit_country/%country_id%',
                                    'locations/backend');
        $this->global_tabs->addItem(_("Delete Country"), 'locations/backend/delete_country/%country_id%',
                                    'locations/backend');
        $this->global_tabs->setParameter('country_id', $country_id);

        $data = Array('country' => (array) $this->locations->getCountry($country_id));

        $this->layout->content = new View('backend/delete_country', $data);
    }
    // }}}
    // {{{ delete_country_validate_read
    public function delete_country_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getCountry'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_country_read_error
    public function delete_country_read_error()
    {
        $this->_invalid_request('locations/backend/list_countries', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_country_write
    public function delete_country_write()
    {
        $this->locations->deleteCountry($this->input->post('country_id'));
        url::redirect('locations/backend/list_countries');
    }
    // }}}
    // {{{ delete_country_validate_write
    public function delete_country_validate_write()
    {
        $this->validation->name('country_id', _("ID"))
             ->add_rules('country_id', 'required', 'valid::numeric', array($this->locations, 'getCountry'));
        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_country_write_error
    public function delete_country_write_error()
    {
        $this->_invalid_request(Null, _("Invalid ID"));
    }
    // }}}
    // }}}
    // }}}
    // {{{ Provinces
    // {{{ list_provinces_any
    public function list_provinces_any($country_id = 0)
    {
        $country = (array) $this->locations->getCountry($country_id);
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $country_id);

        $data = array('flagsaved' => $this->session->get_once('locations_province_saved'));
        $provinces = new PList_Component('provinces');
        $provinces->setResource($this->locations->getProvinces($country_id));
        $provinces->setLimit(Arag_Config::get('limit', 0));
        $provinces->addColumn('id', _("ID"), PList_Component::HIDDEN_COLUMN);
        $provinces->addColumn('province', _("Name"));
        $provinces->addAction('locations/backend/edit_province/#id#', _("Edit"), 'edit_action');
        $provinces->addAction('locations/backend/delete_province/#id#', _("Delete"), 'delete_action');
        $provinces->addAction('locations/backend/list_cities/#id#', _("View cities of this province"), 'view_action');

        $this->layout->content = new View('backend/list_provinces', $data);
    }
    // }}}
    // {{{ list_provinces_validate_any
    public function list_provinces_validate_any()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getCountry'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ list_provinces_any_error
    public function list_provinces_any_error()
    {
        $this->_invalid_request(Null, _("ID is required"));
    }
    // }}}
    // {{{ add_province
    // {{{ add_province_read
    public function add_province_read($country_id = 0)
    {
        $country = (array) $this->locations->getCountry($country_id);
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $country_id);
        $data = array('country' => (array) $this->locations->getCountry($country_id));

        $this->layout->content = new View('backend/add_province', $data);
    }
    // }}}
    // {{{ add_province_validate_read
    public function add_province_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getCountry'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_province_read_error
    public function add_province_read_error()
    {
        $this->_invalid_request(Null, _("ID is required"));
    }
    // }}}
    // {{{ add_province_write
    public function add_province_write()
    {
        $country_id = $this->input->post('country_id', Null, true);
        $province   = $this->input->post('province', Null, true);

        $tempProvince = $this->locations->isProvinceDefined($province, $country_id);
        if (!$tempProvince) {
            $this->locations->createProvince($country_id, $province);
        }

        $this->session->set('locations_province_saved', true);
        url::redirect('locations/backend/list_provinces/' . $country_id);
    }
    // }}}
    // {{{ add_province_validate_write
    public function add_province_validate_write()
    {
        $this->validation->name('country_id', _("Country"))
             ->add_rules('country_id', 'required', 'valid::numeric');

        $this->validation->name('province', _("Province Name"))
             ->pre_filter('trim', 'province')
             ->add_rules('province', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_province_write_error
    public function add_province_write_error()
    {
        $country = (array) $this->locations->getCountry($this->input->post('country_id', Null, true));

        $data = array('country'  => array('id'       => $this->input->post('country_id', Null, true),
                                          'country'  => $this->input->post('country_name', Null, true)),
                      'province' => $this->input->post('province', Null, true));

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $data['country']['id']);

        $this->layout->content = new View('backend/add_province', $data);
    }
    // }}}
    // }}}
    // {{{ edit_province
    // {{{ edit_province_read
    function edit_province_read($province_id = 0)
    {
        $data = (array) $this->locations->getProvince($province_id);
        if ($data) {
            $data['country'] = (array) $this->locations->getCountry($data['country']);
        }
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $data['country']['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Edit Province"), 'locations/backend/edit_province/%province_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $data['country']['id']);
        $this->global_tabs->setParameter('province_id', $province_id);

        $this->layout->content = new View('backend/add_province', $data);
    }
    // }}}
    // {{{ edit_province_validate_read
    public function edit_province_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getProvince'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_province_read_error
    public function edit_province_read_error()
    {
        $this->_invalid_request(Null, _("ID is required"));
    }
    // }}}
    // {{{ edit_province_write
    function edit_province_write()
    {
        $id          = $this->input->post('id', Null, true);
        $province    = $this->input->post('province', Null, true);
        if ($old_province = (array) $this->locations->getProvince($id)) {
            $tempProvince = $this->locations->isProvinceDefined($province, $old_province['country']);
            if (!($tempProvince and ($tempProvince['id'] != $id))) {
                $this->locations->editProvince($id, $old_province['country'], $province);
            }
            $this->session->set('locations_province_saved', true);
            url::redirect('locations/backend/list_provinces/' . $old_province['country']);
        }
    }
    // }}}
    // {{{ edit_province_validate_write
    function edit_province_validate_write()
    {
        $this->validation->name('id', _("Province"))
             ->add_rules('id', 'required', 'valid::numeric');

        $this->validation->name('country_id', _("Country"))
             ->add_rules('country_id', 'required', 'valid::numeric');

        $this->validation->name('province', _("Province Name"))
             ->pre_filter('trim', 'province')
             ->add_rules('province', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_province_write_error
    function edit_province_write_error()
    {
        $id         = $this->input->post('id', Null, true);
        $province   = (array) $this->locations->getProvince($id);
        $country    = (array) $this->locations->getCountry($province['country']);
        $data       = array('id'       => $id,
                            'country'  => $country,
                            'province' => $this->input->post('province', Null, true));

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Edit Province"), 'locations/backend/edit_province/%province_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $data['country']['id']);
        $this->global_tabs->setParameter('province_id', $id);

        $this->layout->content = new View('backend/add_province', $data);
    }
    // }}}
    // }}}
    // {{{ delete_province
    // {{{ delete_province_read
    public function delete_province_read($province_id)
    {
        $province = (array) $this->locations->getProvince($province_id);
        $country  = (array) $this->locations->getCountry($province['country']);

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Edit Province"), 'locations/backend/edit_province/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Delete Province"), 'locations/backend/delete_province/%province_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $province['country']);
        $this->global_tabs->setParameter('province_id', $province_id);

        $data = Array('province' =>  $province);

        $this->layout->content = new View('backend/delete_province', $data);
    }
    // }}}
    // {{{ delete_province_validate_read
    public function delete_province_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getProvince'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_province_read_error
    public function delete_province_read_error()
    {
        $this->_invalid_request('locations/backend/list_countries', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_province_write
    public function delete_province_write()
    {
        $province_id = $this->input->post('province_id');
        $province = (array) $this->locations->getProvince($province_id);
        $this->locations->deleteProvince($province_id);
        url::redirect('locations/backend/list_provinces/' . $province['country']);
    }
    // }}}
    // {{{ delete_province_validate_write
    public function delete_province_validate_write()
    {
        $this->validation->name('province_id', _("ID"))
             ->add_rules('province_id', 'required', 'valid::numeric', array($this->locations, 'getProvince'));
        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_province_write_error
    public function delete_province_write_error()
    {
        $this->_invalid_request(Null, _("Invalid ID"));
    }
    // }}}
    // }}}
    // }}}
    // {{{ Cities
    // {{{ list_cities_any
    public function list_cities_any($province_id = 0)
    {
        $province = (array) $this->locations->getProvince($province_id);
        $country  = (array) $this->locations->getCountry($province['country']);
        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Cities of '%s'"), $province['province']),
                                    'locations/backend/list_cities/%province_id%', 'locations/backend');

        $this->global_tabs->addItem(_("Add City"), 'locations/backend/add_city/%province_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', ($province) ? $province['country'] : Null);
        $this->global_tabs->setParameter('province_id', $province_id);

        $data = array('flagsaved' => $this->session->get_once('locations_city_saved'));
        $cities = new PList_Component('cities');
        $cities->setResource($this->locations->getCities($province_id));
        $cities->setLimit(Arag_Config::get('limit', 0));
        $cities->addColumn('code', _("ID"), PList_Component::HIDDEN_COLUMN);
        $cities->addColumn('city', _("Name"));
        $cities->addAction('locations/backend/edit_city/#code#', _("Edit"), 'edit_action');
        $cities->addAction('locations/backend/delete_city/#code#', _("Delete"), 'delete_action');

        $this->layout->content = new View('backend/list_cities', $data);
    }
    // }}}
    // {{{ list_cities_validate_any
    public function list_cities_validate_any()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getProvince'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ list_cities_any_error
    public function list_cities_any_error()
    {
        $this->_invalid_request(Null, _("ID is required"));
    }
    // }}}
    // {{{ add_city
    // {{{ add_city_read
    public function add_city_read($province_id = 0)
    {
        $province   = (array) $this->locations->getProvince($province_id);
        $country    = (array) $this->locations->getCountry($province['country']);

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Cities of '%s'"), $province['province']),
                                    'locations/backend/list_cities/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add City"), 'locations/backend/add_city/%province_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $country['id']);
        $this->global_tabs->setParameter('province_id', $province_id);

        $data = array('country'     => $country,
                      'province'    => $province);

        $this->layout->content = new View('backend/add_city', $data);
    }
    // }}}
    // {{{ add_city_validate_read
    public function add_city_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getProvince'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_city_read_error
    public function add_city_read_error()
    {
        $this->_invalid_request(Null, _("ID is required"));
    }
    // }}}
    // {{{ add_city_write
    public function add_city_write()
    {
        $province_id    = $this->input->post('province_id', Null, true);
        $city           = $this->input->post('city', Null, true);
        $province       = (array) $this->locations->getProvince($province_id);

        $tempCity = $this->locations->isCityDefined($city, $province_id);
        if (!$tempCity) {
            $this->locations->createCity($province['country'], $province_id, $city);
        }

        $this->session->set('locations_city_saved', true);
        url::redirect('locations/backend/list_cities/' . $province_id);
    }
    // }}}
    // {{{ add_city_validate_write
    public function add_city_validate_write()
    {
        $this->validation->name('province_id', _("Province"))
             ->add_rules('province_id', 'required', 'valid::numeric');

        $this->validation->name('city', _("City Name"))
             ->pre_filter('trim', 'city')
             ->add_rules('city', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_city_write_error
    public function add_city_write_error()
    {
        $data = array('country'     => array('id'       => $this->input->post('country_id', Null, true),
                                             'country'  => $this->input->post('country_name', Null, true)),
                      'province'    => array('id'       => $this->input->post('province_id', Null, true),
                                             'province' => $this->input->post('province_name', Null, true)),
                      'city'        => $this->input->post('city', Null, true));

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $data['country']['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Cities of '%s'"), $data['province']['province']),
                                    'locations/backend/list_cities/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add City"), 'locations/backend/add_city/%province_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $this->input->post('country_id', Null, true));
        $this->global_tabs->setParameter('province_id', $this->input->post('province_id', Null, true));

        $this->layout->content = new View('backend/add_city', $data);
    }
    // }}}
    // }}}
    // {{{ edit_city
    // {{{ edit_city_read
    function edit_city_read($city_id = 0)
    {
        $data = (array) $this->locations->getCity($city_id);
        if ($data) {
            $data['id']         = $data['code'];
            $data['province']   = (array) $this->locations->getProvince($data['province']);
            $data['country']    = (array) $this->locations->getCountry($data['country']);
        }

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $data['country']['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Cities of '%s'"), $data['province']['province']),
                                    'locations/backend/list_cities/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add City"), 'locations/backend/add_city/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Edit City"), 'locations/backend/edit_city/%city_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $data['country']['id']);
        $this->global_tabs->setParameter('province_id', $data['province']['id']);
        $this->global_tabs->setParameter('city_id', $city_id);

        $this->layout->content = new View('backend/add_city', $data);
    }
    // }}}
    // {{{ edit_city_validate_read
    public function edit_city_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_city_read_error
    public function edit_city_read_error()
    {
        $this->_invalid_request(Null, _("ID is required"));
    }
    // }}}
    // {{{ edit_city_write
    function edit_city_write()
    {
        $id     = $this->input->post('id', Null, true);
        $city   = $this->input->post('city', Null, true);

        if ($old_city = (array) $this->locations->getCity($id)) {
            $tempCity = $this->locations->isCityDefined($city, $old_city['province']);
            if (!($tempCity and ($tempCity['code'] != $id))) {
                $this->locations->editCity($id, $old_city['country'], $old_city['province'], $city);
            }
            $this->session->set('locations_city_saved', true);
            url::redirect('locations/backend/list_cities/' . $old_city['province']);
        }
    }
    // }}}
    // {{{ edit_city_validate_write
    function edit_city_validate_write()
    {
        $this->validation->name('id', _("City"))
             ->add_rules('id', 'required', 'valid::numeric');

        $this->validation->name('country_id', _("Country"))
             ->add_rules('country_id', 'required', 'valid::numeric');

        $this->validation->name('province_id', _("Province"))
             ->add_rules('province_id', 'required', 'valid::numeric');

        $this->validation->name('city', _("City Name"))
             ->pre_filter('trim', 'city')
             ->add_rules('city', 'required');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_city_write_error
    function edit_city_write_error()
    {
        $id   = $this->input->post('id', Null, true);
        $data = array('id'       => $id,
                      'country'  => array('id'       => $this->input->post('country_id', Null, true),
                                          'country'  => $this->input->post('country_name', Null, true)),
                      'province' => array('id'       => $this->input->post('province_id', Null, true),
                                          'province'  => $this->input->post('province_name', Null, true)),
                      'city' => $this->input->post('city', Null, true));

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $data['country']['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Cities of '%s'"), $data['province']['province']),
                                    'locations/backend/list_cities/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add City"), 'locations/backend/add_city/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Edit City"), 'locations/backend/edit_city/%city_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $data['country']['id']);
        $this->global_tabs->setParameter('province_id', $data['province']['id']);
        $this->global_tabs->setParameter('city_id', $id);

        $this->layout->content = new View('backend/add_city', $data);
    }
    // }}}
    // }}}
    // {{{ delete_city
    // {{{ delete_city_read
    public function delete_city_read($city_id)
    {
        $city       = (array) $this->locations->getCity($city_id);
        $province   = (array) $this->locations->getProvince($city['province']);
        $country    = (array) $this->locations->getCountry($city['country']);

        $this->global_tabs->addItem(_("Countries"), 'locations/backend/list_countries', 'locations/backend');
        $this->global_tabs->addItem(_("Add Country"), 'locations/backend/add_country', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Provinces of '%s'"), $country['country']),
                                    'locations/backend/list_provinces/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add Province"), 'locations/backend/add_province/%country_id%', 'locations/backend');
        $this->global_tabs->addItem(sprintf(_("Cities of '%s'"), $province['province']),
                                    'locations/backend/list_cities/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Add City"), 'locations/backend/add_city/%province_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Edit City"), 'locations/backend/edit_city/%city_id%', 'locations/backend');
        $this->global_tabs->addItem(_("Delete City"), 'locations/backend/delete_city/%city_id%', 'locations/backend');
        $this->global_tabs->setParameter('country_id', $city['country']);
        $this->global_tabs->setParameter('province_id', $city['province']);
        $this->global_tabs->setParameter('city_id', $city_id);

        $data = Array('city' =>  $city);

        $this->layout->content = new View('backend/delete_city', $data);
    }
    // }}}
    // {{{ delete_city_validate_read
    public function delete_city_validate_read()
    {
        $this->validation->name(0, _("ID"))->add_rules(0, 'required', 'valid::numeric', array($this->locations, 'getCity'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_city_read_error
    public function delete_city_read_error()
    {
        $this->_invalid_request('locations/backend/list_countries', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_city_write
    public function delete_city_write()
    {
        $city_id = $this->input->post('city_id');
        $city = (array) $this->locations->getCity($city_id);
        $this->locations->deleteCity($city_id);
        url::redirect('locations/backend/list_cities/' . $city['province']);
    }
    // }}}
    // {{{ delete_city_validate_write
    public function delete_city_validate_write()
    {
        $this->validation->name('city_id', _("ID"))
             ->add_rules('city_id', 'required', 'valid::numeric', array($this->locations, 'getCity'));
        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_city_write_error
    public function delete_city_write_error()
    {
        $this->_invalid_request(Null, _("Invalid ID"));
    }
    // }}}
    // }}}
    // }}}
}
