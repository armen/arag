<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ construct
    public function __construct() {
        parent::__construct();

        $this->validation->message('required', _("%s is required"));

        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Calendar"));
        $this->global_tabs->addItem(_("List"), 'calendar/backend');
        $this->global_tabs->addItem(_("Add"), 'calendar/backend/add');

        $this->calendar = Model::load('Calendar', 'calendar');
    }
    // }}}
    // {{{ index
    public function index()
    {
        $holidays = New Plist_Component('holidays');
        $holidays->setResource($this->calendar->getAll());
        $holidays->addColumn('format::show_date[date]', _("Date"), PList_Component::VIRTUAL_COLUMN);
        $holidays->addColumn('Backend_Controller::_description', _("Description"), PList_Component::VIRTUAL_COLUMN);
        $holidays->addAction('calendar/backend/edit/#id#', 'Edit', 'edit_action');
        $holidays->addAction('calendar/backend/delete/#id#', 'Delete', 'delete_action');

        $this->layout->content = New View('backend/index');
    }
    // }}}
    // {{{ _description
    public function _description($row)
    {
        return strip_tags(html_entity_decode($row['description']));
    }
    // }}}
    // {{{ add_read
    public function add_read()
    {
        $this->layout->content              = New View('backend/add');
        $this->layout->content->date        = date::get_time('date');
        $this->layout->content->description = $this->input->post('description');
    }
    // }}}
    // {{{ add_validate_write
    public function add_validate_write() {
        $this->validation->name('date', _("Date"))->add_rules('date', 'required', 'date[date]');
        $this->validation->name('description', _("Description"))->add_rules('description', 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ add_write_error
    public function add_write_error() {
        $this->add_read();
    }
    // }}}
    // {{{ add_write
    public function add_write() {
        $this->calendar->add(date::get_time('date'), $this->input->post('description'));

        url::redirect('calendar/backend/index');
    }
    // }}}
    // {{{ edit_read
    public function edit_read($id)
    {
        $holiday                            = $this->calendar->get($id);
        $this->layout->content              = New View('backend/edit');
        $this->layout->content->id          = $holiday['id'];
        $this->layout->content->date        = $holiday['date'];
        $this->layout->content->description = $holiday['description'];
    }
    // }}}
    // {{{ edit_validate_write
    public function edit_validate_write($id) {
        $this->validation->name('date', _("Date"))->add_rules('date', 'required', 'date[date]');
        $this->validation->name('description', _("Description"))->add_rules('description', 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error($id) {
        $this->edit_read($id);
    }
    // }}}
    // {{{ edit_write
    public function edit_write($id) {
        $this->calendar->edit($id, date::get_time('date'), $this->input->post('description'));

        url::redirect('calendar/backend/index');
    }
    // }}}
    // {{{ delete_read
    public function delete_read($id)
    {
        $holiday                        = $this->calendar->get($id);
        $this->layout->content          = New View('backend/delete');
        $this->layout->content->holiday = $holiday;
    }
    // }}}
    // {{{ delete_validate_read
    public function delete_validate_read()
    {
        $this->validation->add_rules(0, 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_read_error
    public function delete_read_error()
    {
        $this->_invalid_request();
    }
    // }}}
    // {{{ delete_validate_write  TODO: a bug in arag/kohana prevents this from working.
//     public function delete_validate_write()
//     {
//         $this->validation->add_rules(0, 'required');
//         return $this->validation->validate();
//     }
    // }}}
    // {{{ delete_write_error
    public function delete_write_error()
    {
        $this->_invalid_request();
    }
    // }}}
    // {{{ delete_write
    public function delete_write($id)
    {
        $this->calendar->delete($id);
        url::redirect('calendar/backend/index');
    }
    // }}}
}
