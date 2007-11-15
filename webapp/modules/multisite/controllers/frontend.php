<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:             
// +-------------------------------------------------------------------------+
// | Author: Sasan Rose <sasan.rose@gmail.com>                               |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller 
{   
    // {{{ Constructor
    function __construct()
    {
        parent::__construct();

        // Load the model
        $this->load->model('MultiSite');        
        $this->load->model('Users', NULL, 'user');

        // Backend decorator
        $this->load->decorator('frontend/decorator');

        // Default page title
        $this->decorator->page_title = _("MultiSite");
    }
    // }}}
    // {{{ index_read
    public function index_read($verify_uri = false)
    {   
        $error_message = false;
        $show_form     = true;
        
        if ($this->session->get_once('multi_site_status')) {
            $error_message = _("Wrong username or password!");

        } else if (!$verify_uri || !$this->Users->hasUri($verify_uri)) {
            $error_message = _("Please enter a valid uri!");
            $show_form     = false;

        } else if (Arag_Config::get('expire', 0) != 0) {
            if ((time()-$this->Users->createDate($verify_uri)) > (Arag_Config::get('expire', 0) * 3600)) {
                $error_message = _("This uri is expired! Please contact site administrator for further information.");
                $show_form     = false;            
            }
        }

        $data = array('error_message' => $error_message,
                      'show_form'     => $show_form,
                      'uri'           => $verify_uri);
        $this->load->view('frontend/verify', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        $verify_uri = $this->input->post('uri');
        $username   = $this->input->post('username');
        $password   = $this->input->post('password');

        if ($this->Users->checkVerify($username, $password, $verify_uri)) {
            $this->Users->verify($username, $password, $verify_uri);
            $this->load->view('frontend/verified');
                    
        } else {
            $this->session->set('multi_site_status', True);
            url::redirect('multisite/frontend/index/'.$verify_uri);
        }
    }
    // }}}
}
?>
