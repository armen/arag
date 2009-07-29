<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Authors: Sasan Rose <sasan.rose@gmail.com>                              |
// |          Peyman Karimi <zeegco@yahoo.com>                               |
// |          Armen Baghumian <armen@OpenSourceClub.org>                     |
// |          Emil Sedgh <emilsedgh@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Locations"));
        $this->global_tabs->addItem(_("Locations"), 'locations/backend');
        $this->global_tabs->addItem(_("Google api keys"), 'locations/backend/google_api_keys');

        // Load the models
        $this->locations = new Locations_Model;

        // Default page title
        $this->layout->page_title = _("Locations");

        $this->validation->message('required', _("%s is required"));
        $this->validation->message('standard_text', _("%s must be English text"));
        $this->validation->message('numeric', _("%s must be number"));
    }
    // }}}
    // {{{ index_any
    public function index_any($parent = 0)
    {
        $locations = New PList_Component('locations');
        $locations->setResource($this->locations->getByParent($parent));
        $locations->addColumn('Backend_Controller::_flag', _("Flag"), PList_Component::VIRTUAL_COLUMN);
        $locations->addColumn('code', _("Code"));
        $locations->addColumn('english', _("English"));
        $locations->addColumn('name', _("Name"));
        $locations->addColumn('Backend_Controller::_type', _("Type"), PList_Component::VIRTUAL_COLUMN);
        $locations->addColumn('latitude', _("Latitude"));
        $locations->addColumn('longitude', _("Longitude"));
        $locations->addAction('locations/backend/index/#id#', _("View children"), 'view_action');
        $locations->addAction('locations/backend/edit/#id#', _("Edit"), 'edit_action');
        $locations->addAction('locations/backend/add/#id#', _("Add child"), 'apply_action');

        $this->layout->content = New View('backend/index');
    }
    // }}}
    // {{{ _flag
    public static function _flag($row)
    {
        return html::image(url::base().'/modpub/locations/images/'.$row['type'].'/'.strtolower($row['code']).'/flag.png');
    }
    // }}}
    // {{{ _type
    public static function _type($row)
    {
        return Model::load('Locations', 'locations')->names[$row['type']];
    }
    // }}}
    // {{{ add_read
    public function add_read($parent=0)
    {
        $this->layout->content            = New View('backend/add');
        $this->layout->content->parent    = $parent;
        $this->layout->content->english   = $this->input->post('english');
        $this->layout->content->name      = $this->input->post('name');
        $this->layout->content->code      = $this->input->post('code');
        $this->layout->content->types     = $this->locations->names;
        $this->layout->content->latitude  = $this->input->post('latitude');
        $this->layout->content->longitude = $this->input->post('longitude');
    }
    // }}}
    // {{{ add_validate_write
    public function add_validate_write()
    {
        $this->validation->name('english', _("English name"))->add_rules('english', 'standard_text');
        $this->validation->name('name', _("Name"))->add_rules('name');
        $this->validation->name('code', _("Code"))->add_rules('code', 'alpha');
        $this->validation->name('latitude', _("Latitude"))->add_rules('latitude', 'numeric');
        $this->validation->name('longitude', _("Longitude"))->add_rules('longitude', 'numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ add_write_error
    public function add_write_error($parent = 0)
    {
        $this->add_read($parent);
    }
    // }}}
    // {{{ add_write
    public function add_write($parent)
    {
        $this->locations->add($parent, $this->input->post('english'), $this->input->post('type'), $this->input->post('code'), $this->input->post('name'), $this->input->post('latitude'), $this->input->post('longitude'));
        url::redirect('locations/backend/index/'.$parent);
    }
    // }}}
    // {{{ edit_read
    public function edit_read($id)
    {
        $this->layout->content = New View('backend/edit');
        $this->layout->content->location = $this->locations->get($id);
        $this->layout->content->types = $this->locations->names;
    }
    // }}}
    // {{{ edit_validate_write
    public function edit_validate_write()
    {
        $this->validation->name('english', _("English name"))->add_rules('english', 'standard_text');
        $this->validation->name('name', _("Name"))->add_rules('name');
        $this->validation->name('code', _("Code"))->add_rules('code', 'alpha');
        $this->validation->name('latitude', _("Latitude"))->add_rules('latitude', 'numeric');
        $this->validation->name('longitude', _("Longitude"))->add_rules('longitude', 'numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error($id)
    {
        $this->edit_read($id);
    }
    // }}}
    // {{{ edit_write
    public function edit_write($id)
    {
        $this->locations->edit($id, 0, $this->input->post('english'), $this->input->post('type'), $this->input->post('code'), $this->input->post('name'), $this->input->post('latitude'), $this->input->post('longitude'));
        $this->edit_read($id);
    }
    // }}}
    // {{{ delete_read
    public function delete_read($id)
    {
        $this->layout->content           = New View('backend/delete');
        $this->layout->content->location = $this->locations->get($id);
    }
    // }}}
    // {{{ delete_write
    public function delete_write($id)
    {
       $this->locations->delete($id);
       url::redirect('locations/backend');
    }
    // }}}
    // {{{ google_api_keys_read
    public function google_api_keys_read()
    {
        $keys = New PList_Component('google_api_keys');
        $keys->setResource($this->locations->getKeys());
        $keys->addcolumn('domain', _("Domain"));
        $keys->addColumn('key', _("API Key"));
        $keys->addAction('locations/backend/google_api_keys_delete/#domain#', _("Delete"), 'delete_action');

        $this->layout->content                 = New View('backend/google_api_keys');
        $this->layout->content->domain         = $this->input->post('domain');
        $this->layout->content->google_api_key = $this->input->post('google_api_key');
    }
    // }}}
    // {{{ google_api_key_validate_write
    public function google_api_keys_validate_write()
    {
        $this->validation->name('domain', _("Domain"))->add_rules('domain', 'required');
        $this->validation->name('google_api_key', _("Google API key"))->add_rules('domain', 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ google_api_key_write_error
    public function google_api_keys_write_error()
    {
        $this->google_api_keys_read();
    }
    // }}}
    // {{{ google_api_keys_write
    public function google_api_keys_write()
    {
        $this->locations->addKey($this->input->post('domain'), $this->input->post('google_api_key'));
        $this->google_api_keys_read();
    }
    // }}}
    // {{{ google_api_keys_delete_read
    public function google_api_keys_delete_read($domain)
    {
        $this->locations->deleteKey($domain);
        url::redirect('locations/backend/google_api_keys');
    }
    // }}}
    // }}}
}
