<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author:    Sasan Rose <sasan.rose@gamil.com                             |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Backend_Controller extends Controller 
{
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();
       
        // Default page title
        $this->layout->page_title = _("Static Pages");

        // Global tabbedbock
        $this->load->component('TabbedBlock', 'global_tabs');
        $this->global_tabs->setTitle(_("StaticPages"));
        $this->global_tabs->addItem(_("List"), 'staticpages/backend/index');
        $this->global_tabs->addItem(_("Create"), 'staticpages/backend/create');
        $this->global_tabs->addItem(_("Settings"), 'staticpages/backend/settings');
        $this->global_tabs->addItem(_("Edit"), 'staticpages/backend/edit/%id%', 'staticpages/backend/index');
        $this->global_tabs->addItem(_("Preview"), 'staticpages/backend/preview/%id%', 'staticpages/backend/index');
        $this->global_tabs->addItem(_("Delete"), 'staticpages/backend/delete/%id%', 'staticpages/backend/index');
    }
    // }}}
    // {{{ index
    public function index()
    {
        $this->load->model('StaticPages');

        $this->load->component('PList', 'staticpages');

        $this->staticpages->setResource($this->StaticPages->getPages());
        $this->staticpages->setLimit(Arag_Config::get('limit', 0));
        $this->staticpages->addColumn('id', Null, PList::HIDDEN_COLUMN);
        $this->staticpages->addColumn('subject', _("Subject"));        
        $this->staticpages->addColumn('author', _("Author"));
        $this->staticpages->addColumn('StaticPages.getDate', _("Create Date"), PList::VIRTUAL_COLUMN);
        $this->staticpages->addColumn('StaticPages.getModifyDate', _("Modify Date"), PList::VIRTUAL_COLUMN);
        $this->staticpages->addAction('staticpages/backend/edit/#id#', _("Edit"), 'edit_action');
        $this->staticpages->addAction('staticpages/backend/delete/#id#', _("Delete"), 'delete_action');
        $this->staticpages->addAction('staticpages/backend/preview/#id#', _("Preview"), 'view_action');
        $this->staticpages->addAction('staticpages/backend/gdelete', _("Delete"), 'delete_action', PList::GROUP_ACTION);
        $this->staticpages->setGroupActionParameterName('id');        
        
        $this->layout->content = new View('backend/index');
    }
    // }}}
    // {{{ create_read
    public function create_read()
    {
        $this->layout->content = new View('backend/create');
    }
    // }}}
    // {{{ edit_read
    public function edit_read($id = Null)
    {
        $this->load->model('StaticPages');
        
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

            $this->layout->content = new View('backend/edit', $data);
        } else {
            $this->_invalid_request("staticpages/backend/index");
        }
    }
    // }}}
    // {{{ create_write
    public function create_write()
    {
       $this->load->model('StaticPages');
      
       if ($this->input->post('submit')){

            $page    = $this->input->post('page', True);
            $subject = $this->input->post('subject', True);

            $this->StaticPages->createPage($this->session->get('username'), $subject, $page);
            
            url::redirect('staticpages/backend/index');
        } else {
            $this->_invalid_request("staticpages/backend/index");
        }
    }
    // }}}
    // {{{ edit_write
    public function edit_write($id = Null)
    {
        $this->load->model('StaticPages');

        $exist = false;        

        if (is_numeric($id) && $this->input->post('submit')) {
            $exist = $this->StaticPages->checkID($id);
        }
        
        if ($exist) { 
            $page    = $this->input->post('page', True);
            $subject = $this->input->post('subject', True);

            $this->StaticPages->editPage($id, $subject, $page);
        
            url::redirect('staticpages/backend/index');
        } else {
            $this->_invalid_request("staticpages/backend/index");
        }
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
    // {{{ create_write_error
    public function create_write_error()
    {   
        $this->create_read();
    }
    // }}}
    // {{{ delete
    public function delete($id = Null)
    {
        $this->load->model('StaticPages');

        $subjects = array();
        $exist    = false;

        if (is_numeric($id)) {
            $exist = $this->StaticPages->checkID($id);
        }

        if ($exist) {

            $this->global_tabs->setParameter('id', $id);

            $row = $this->StaticPages->getPage($id);
            
            $this->layout->content = new View('backend/delete', array('ids' => array($id), 'subjects' => $row['subject']));

        } else {
            $this->_invalid_request("staticpages/backend/index");
        }
    }
    // }}}
    // {{{ gdelete
    public function gdelete($id = Null)
    {
        $this->load->model('StaticPages');

        if ($this->input->post('id')) {

            $ids      = $this->input->post('id');
            $subjects = array();

            foreach ($ids as $key) {

                $exist = false;

                if (is_numeric($key)) {
                    $exist = $this->StaticPages->checkID($key);
                }

                if (!$exist) {
                    $this->_invalid_request("staticpages/backend/index");
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
            $this->_invalid_request('staticpages/backend/index/');
        }
    }
    // }}}
    // {{{ do_delete
    public function do_delete()
    {
        $this->load->model('StaticPages');
        
        if (isset($_POST['submit'])) {
            $ids = $this->input->post('id');
                        
            foreach ($ids as $key) {
                $this->load->model('StaticPages');
                $this->StaticPages->deletePage($key);
            }

            url::redirect('staticpages/backend/index');

        } else {
            $this->_invalid_request("staticpages/backend/index");
        }
    }
    // }}}
    // {{{ preview
    public function preview($id = Null)
    {
        $this->load->model('StaticPages');
        
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
            $this->_invalid_request("staticpages/backend/index");
        }
    }
    // }}}
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
    // {{{ settings_write_error
    public function settings_write_error()
    {
        $this->settings_read();
    }
    // }}}
}
?>
