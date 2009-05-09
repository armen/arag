<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Properties
    private $model;
    private $groups;
    // }}}

    // {{{ __construct
    public function __construct()
    {
        parent::__construct();

        $this->model  = Model::load('HelpManager', 'help');
        $this->groups = Model::load('Groups', 'user');

        // load global Tabs
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Helps"));
        $this->global_tabs->addItem(_("Index"), 'help/backend/index');
        $this->global_tabs->addItem(_("Listing"), 'help/backend/listing/%uri%', 'help/backend/index');
        $this->global_tabs->addItem(_("Add"), 'help/backend/add/%uri%', 'help/backend/index');
    }
    // }}}
    // {{{ _invalid_request
    public function _invalid_request()
    {
        parent::_invalid_request(null, _('Invalid uri'));
    }
    // }}}
    // {{{ index
    public function index()
    {
        $uris = $this->model->getUris();

        $list = new PList_Component('uris');
        $list->setResource($this->model->getUris());
        $list->addColumn('decoded', _("Url"));
        $list->addAction('help/backend/listing/#encoded#', _("View"), 'view_action');

        $this->layout->content = new View('backend/index');
    }
    // }}}
    // {{{ listing_read
    public function listing_read($uri)
    {
        $this->global_tabs->setParameter('uri', $uri);

        $helps = new PList_Component('helps');
        $helps->setResource($this->model->getByUri($uri, True, False));
        $helps->addColumn('title', _("Title"));
        $helps->addColumn('HelpManager.translatedType', _("Type"), PList_Component::VIRTUAL_COLUMN);
        $helps->addColumn('HelpManager.viewers', _("Viewers"), PList_Component::VIRTUAL_COLUMN);
        $helps->addAction('help/backend/edit/#id#', _("Edit"), 'edit_action');
        $helps->addAction('help/backend/delete/#id#', _("Delete"), 'delete_action');

        $this->layout->content = new View('backend/listing');
    }
    // }}}
    // {{{ listing_validate_read
    public function listing_validate_read($uri=null)
    {
        $this->validation->add_rules(0, 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ listing_read_error
    public function listing_read_error($uri=null)
    {
        $this->_invalid_request();
    }
    // }}}
    // {{{ add_read
    public function add_read($uri)
    {
        $this->global_tabs->setParameter('uri', $uri);

        $groups = $this->groups->getGroups(APPNAME);

        $this->layout->content          = new View('backend/add');
        $this->layout->content->uri     = $uri;
        $this->layout->content->dialogs = $this->model->dialogs;
        $this->layout->content->groups  = $groups;
    }
    // }}}
    // {{{ add_validate_read
    public function add_validate_read($uri=null)
    {
        $this->validation->name(0, _("Uri"))->add_rules(0, 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ add_read_error
    public function add_read_error($uri=null)
    {
        $this->_invalid_request();
    }
    // }}}
    // {{{ add_write
    public function add_write()
    {
        $uri    = $this->input->post('uri');
        $id     = $this->model->add($uri, $this->input->post('title'), $this->input->post('message'), $this->input->post('type'));
        $groups = $this->input->post('groups');

        if (is_array($groups)) {
            foreach($groups as $group_id=>$nonsense) {
                $this->model->allow($id, $group_id);
            }
        }
        url::redirect('help/backend/listing/'.$uri);
    }
    // }}}
    // {{{ add_validate_write
    public function add_validate_write()
    {
        $this->validation->add_rules('uri', 'required');
        return $this->validation->validate();
    }
    // }}}
    // {{{ add_write_error
    public function add_write_error()
    {
        $this->_invalid_request();
    }
    // }}}
    // {{{ edit_read
    public function edit_read($id)
    {
        $help   = $this->model->get($id);
        $groups = $this->groups->getGroups(APPNAME);

        $this->global_tabs->setParameter('id', $help->id);
        $this->global_tabs->setParameter('uri', $help->uri);
        $this->global_tabs->addItem(_("Edit"), 'help/backend/edit/%id%', 'help/backend/index');
        $this->global_tabs->addItem(_("Delete"), 'help/backend/delete/%id%', 'help/backend/index');

        foreach($groups as $group)
        {
            $group['isAllowed'] = $this->model->isAllowed($id, $group['id']);
            $groupList[] = $group; //groups is an iterator, but from a db result.so its untouchable.we store the new group array in another array
        }

        $this->layout->content          = new View('backend/edit');
        $this->layout->content->help    = $help;
        $this->layout->content->dialogs = $this->model->dialogs;
        $this->layout->content->groups  = $groupList;
    }
    // }}}
    // {{{ edit_validate_read
    public function edit_validate_read()
    {
        $this->validation->add_rules(0, 'required', 'valid::numeric');
        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_read_error
    public function edit_read_error()
    {
        parent::_invalid_request(null, 'Invalid id');
    }
    // }}}
    // {{{ edit_write
    public function edit_write()
    {
        $id = $this->input->post('id');

        $this->model->edit($id, $this->input->post('title'), $this->input->post('message'), $this->input->post('type'));

        $groups         = $this->groups->getGroups();
        $allowed_groups = $this->input->post('groups');

        foreach($groups as $group) {
            if(isset($allowed_groups[$group['id']])) {
                $this->model->allow($id, $group['id']);

            } else {
                $this->model->deny($id, $group['id']);
            }
        }

        $help = $this->model->get($id);
        url::redirect('help/backend/listing/'.$help->uri);
    }
    // }}}
    // {{{ edit_validate_write
    public function edit_validate_write()
    {
        $this->validation->add_rules('id', 'required', 'valid::numeric');
        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error()
    {
        parent::_invalid_request(null, 'Invalid id');
    }
    // }}}
    // {{{ delete_read
    public function delete_read($id)
    {
        $help   = $this->model->get($id);

        $this->global_tabs->setParameter('id', $help->id);
        $this->global_tabs->setParameter('uri', $help->uri);
        $this->global_tabs->addItem(_("Edit"), 'help/backend/edit/%id%', 'help/backend/index');
        $this->global_tabs->addItem(_("Delete"), 'help/backend/delete/%id%', 'help/backend/index');

        $this->layout->content       = new View('backend/delete');
        $this->layout->content->help = $help;
    }
    // }}}
    // {{{ delete_validate_read
    public function delete_validate_read()
    {
        $this->validation->add_rules(0, 'required', 'valid::numeric');
        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_read_error
    public function delete_read_error()
    {
        parent::_invalid_request(null, 'Invalid id');
    }
    // }}}
    // {{{ delete_write
    public function delete_write()
    {
        $id = $this->input->post('id');
        $help = $this->model->get($id);
        $this->model->delete($id);
        url::redirect('help/backend/listing/'.$help->uri);
    }
    // }}}
    // {{{ delete_validate_write
    public function delete_validate_write()
    {
        $this->validation->add_rules('id', 'required', 'valid::numeric');
        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_write_error
    public function delete_write_error()
    {
        parent::_invalid_request(null, 'Invalid id');
    }
    // }}}
}
