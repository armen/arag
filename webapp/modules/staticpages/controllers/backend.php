<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author:    Sasan Rose <sasan.rose@gamil.com                             |
// |            Jila Khaghani <jilakhaghani@gmail.com>                       |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        $this->available_routes =Kohana::Config('config.available_routes', null);

        foreach ($this->available_routes as &$avelaible) {
            $avelaible = _($avelaible);
        }

        // Default page title
        $this->layout->page_title = _("Static Pages");

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("StaticPages"));
        $this->global_tabs->addItem(_("List"), 'staticpages/backend/index');
        $this->global_tabs->addItem(_("Create"), 'staticpages/backend/create');
        $this->global_tabs->addItem(_("Settings"), 'staticpages/backend/settings');
        $this->global_tabs->addItem(_("Edit"), 'staticpages/backend/edit/%id%', 'staticpages/backend/index');
        $this->global_tabs->addItem(_("Preview"), 'staticpages/backend/preview/%id%', 'staticpages/backend/index');
        $this->global_tabs->addItem(_("Delete"), 'staticpages/backend/delete/%id%', 'staticpages/backend/index');

        // Validation Messages
        $this->validation->message('_select_route', _("%s should define"));
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric"));
    }
    // }}}
    // {{{ index
    public function index_any()
    {
        $this->StaticPages = new StaticPages_Model;

        $this->staticpages = new PList_Component('staticpages');

        $this->staticpages->setResource($this->StaticPages->getPages());
        $this->staticpages->setLimit(Arag_Config::get('limit', 0));
        $this->staticpages->addColumn('id', Null, PList_Component::HIDDEN_COLUMN);
        $this->staticpages->addColumn('id', _("ID"));
        $this->staticpages->addColumn('subject', _("Subject"));
        $this->staticpages->addColumn('author', _("Author"));
        $this->staticpages->addColumn('StaticPages.getDate', _("Create Date"), PList_Component::VIRTUAL_COLUMN);
        $this->staticpages->addColumn('StaticPages.getModifyDate', _("Modify Date"), PList_Component::VIRTUAL_COLUMN);
        $this->staticpages->addColumn('route', _("Route"));
        $this->staticpages->addAction('staticpages/backend/preview/#id#', _("Preview"), 'view_action');
        $this->staticpages->addAction('staticpages/backend/edit/#id#', _("Edit"), 'edit_action');
        $this->staticpages->addAction('staticpages/backend/delete/#id#', _("Delete"), 'delete_action');
        $this->staticpages->addAction('staticpages/backend/gdelete', _("Delete"), 'delete_action', False, PList_Component::GROUP_ACTION);
        $this->staticpages->setGroupActionParameterName('id');

        $this->layout->content = new View('backend/index');
    }
    // }}}
    // {{{ create_read
    public function create_read()
    {
        $this->layout->content = new View('backend/create');
        $this->layout->content->available_routes = $this->available_routes;
    }
    // }}}
    // {{{ edit_read
    public function edit_read($id = Null)
    {
        $this->StaticPages = new StaticPages_Model;

        $exist = false;

        if (is_numeric($id)) {
            $exist = $this->StaticPages->checkID($id);
        }

        if ($exist) {

            $this->global_tabs->setParameter('id', $id);

            $row = $this->StaticPages->getPage($id);

            $data = Array('id'      => $row['id'],
                          'subject' => $row['subject'],
                          'page'    => $row['page'],
                          'route'   => $row['route']);

            $this->layout->content = new View('backend/edit', $data);
            $this->layout->content->available_routes = $this->available_routes;
        } else {
            $this->_invalid_request("staticpages/backend/index", _("Invalid ID"));
        }
    }
    // }}}
    // {{{ create_write
    // {{{ create_write
    public function create_write()
    {
        $this->StaticPages = new StaticPages_Model;

        if ($this->input->post('submit')){

            $page    = $this->input->post('page', Null, True);
            $subject = $this->input->post('subject', Null, True);

            $route   = $this->input->post('available_route', Null)
                                         ? $this->input->post('available_route', Null):
                                         $this->input->post('new_route', Null);

            $this->StaticPages->createPage($this->session->get('user.username'), $subject, $page, $route);

            url::redirect('staticpages/backend/index');
        } else {
            $this->_invalid_request('staticpages/backend/index', _("No form is submitted"));
        }
    }
    // }}}
    // {{{ create_validate_write
    public function create_validate_write()
    {
        $this->validation->name('subject', _("Subject"))->pre_filter('trim', 'subject')
             ->add_rules('subject', 'required', 'standard_text');

        $this->validation->name('page', _("Page"))->add_rules('page', 'required')
             ->post_filter('security::xss_clean', 'page');

        $this->validation->name('routes', _("Route"))
             ->add_rules('routes', array($this, '_select_route'));

        return $this->validation->validate();
    }
    // }}}
    // {{{ create_write_error
    public function create_write_error()
    {
        $this->create_read();
    }
    // }}}
    // }}}
    // {{{ edit_write
    // {{{ edit_write
    public function edit_write($id = Null)
    {
        $this->StaticPages = new StaticPages_Model;

        $exist = false;

        if (is_numeric($id) && $this->input->post('submit')) {
            $exist = $this->StaticPages->checkID($id);
        }

        if ($exist) {
            $page    = $this->input->post('page', Null, True);
            $subject = $this->input->post('subject', Null, True);
            $route   = $this->input->post('available_route', Null) ?
                                     $this->input->post('available_route', Null)
                                    :$this->input->post('new_route', Null);

            $this->StaticPages->editPage($id, $subject, $page,$route);

            url::redirect('staticpages/backend/index');
        } else {
            $this->_invalid_request("staticpages/backend/index", _("Invalid ID"));
        }
    }
    // }}}
    // {{{ edit_validate_write
    public function edit_validate_write()
    {
        $this->validation->name('id', _("id"))->add_rules('id', 'required', 'numeric');

        $this->validation->name('subject', _("Subject"))->pre_filter('trim', 'subject')
             ->add_rules('subject', 'required', 'standard_text');

        $this->validation->name('page', _("Page"))->add_rules('page', 'required')
             ->post_filter('security::xss_clean', 'page');

        $this->validation->name('routes', _("Route"))
             ->add_rules('routes', array($this, '_select_route'));


        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error()
    {
        $id = $this->input->post('id');
        $this->global_tabs->setParameter('id', $id);

        $this->layout->content = new View('backend/edit', Array('id' => $id));
    }
    // }}}
    // }}}
    // {{{ delete
    public function delete_any($id = Null)
    {
        $this->StaticPages = new StaticPages_Model;

        $subjects = array();
        $exist    = false;

        if (is_numeric($id)) {
            $exist = $this->StaticPages->checkID($id);
        }

        if ($exist) {

            $this->global_tabs->setParameter('id', $id);

            $row = $this->StaticPages->getPage($id);

            $this->layout->content = new View('backend/delete'
                                              , array('ids' => array($id)
                                              , 'subjects' => $row['subject']));

        } else {
            $this->_invalid_request("staticpages/backend/index", _("Invalid ID"));
        }
    }
    // }}}
    // {{{ gdelete
    public function gdelete_any($id = Null)
    {
        $this->StaticPages = new StaticPages_Model;

        if ($this->input->post('id')) {

            $ids      = $this->input->post('id');
            $subjects = array();

            foreach ($ids as $key) {

                $exist = false;

                if (is_numeric($key)) {
                    $exist = $this->StaticPages->checkID($key);
                }

                if (!$exist) {
                    $this->_invalid_request("staticpages/backend/index", _("Invalid ID"));
                }

                $this->global_tabs->setParameter('id', $key);

                $row        = $this->StaticPages->getPage($key);
                $subjects[] = $row['subject'];
            }

            $subjects = implode(",", $subjects);

            $data = array('ids'      => $ids,
                          'subjects' => $subjects);
            $this->layout->content = new View('backend/delete', $data);

        } else {
            $this->_invalid_request('staticpages/backend/index/', _("Invalid ID"));
        }
    }
    // }}}
    // {{{ do_delete
    public function do_delete_any()
    {
        $this->StaticPages = new StaticPages_Model;

        if (isset($_POST['submit'])) {
            $ids = $this->input->post('id');

            foreach ($ids as $key) {
                $this->StaticPages = new StaticPages_Model;
                $this->StaticPages->deletePage($key);
            }

            url::redirect('staticpages/backend/index');

        } else {
            $this->_invalid_request("staticpages/backend/index", _("No form is submitted"));
        }
    }
    // }}}
    // {{{ preview
    public function preview_any($id = Null)
    {
        $this->StaticPages = new StaticPages_Model;

        $exist = false;

        if (is_numeric($id)) {
            $exist = $this->StaticPages->checkID($id);
        }

        if ($exist) {

            $this->global_tabs->setParameter('id', $id);

            $row = $this->StaticPages->getPage($id);

            $data = Array('id'      => $row['id'],
                          'subject' => $row['subject'],
                          'page'    => $row['page']);

            $this->layout->content = new View('backend/preview', $data);

        } else {
            $this->_invalid_request("staticpages/backend/index", _("Invalid ID"));
        }
    }
    // }}}
    // {{{ settings
    // {{{ settings_read
    public function settings_read($saved = NULL)
    {
        $data          = Array();
        $data['limit'] = Arag_Config::get("limit");
        $data['saved'] = $this->session->get_once('configuration_saved');

        $this->layout->content = new View('backend/settings', $data);
    }
    // }}}
    // {{{ settings_write
    public function settings_write()
    {
        Arag_Config::set('limit', $this->input->post('limit'));
        $this->session->set('configuration_saved', True);

        url::redirect('staticpages/backend/settings');
    }
    // }}}
    // {{{ settings_validate_write
    public function settings_validate_write()
    {
        $this->validation->name('limit', _("Limit"))->pre_filter('trim', 'limit')
             ->add_rules('limit', 'required', 'numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ settings_write_error
    public function settings_write_error()
    {
        $this->settings_read();
    }
    // }}}
    // }}}
    // {{{ _select_route
    public function _select_route($route)
    {
        $available_route = $this->input->post('available_route');
        $new_route       = $this->input->post('new_route');

        if (empty($available_route) && empty($new_route)) {
            return False;
        }

        return True;
    }
    // }}}
}
