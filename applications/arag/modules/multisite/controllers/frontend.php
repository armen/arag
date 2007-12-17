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

        // Default page title
        $this->layout->page_title = _("MultiSite");
    }
    // }}}
    // {{{ index_read
    public function index_read($verify_uri = false)
    {   
        $show_form     = true;
        $error_message = false;
        
        if (!$verify_uri || !$this->Users->hasUri($verify_uri)) {
            $error_message = _("Please enter a valid uri!");
            $show_form     = false;

        } else if (Arag_Config::get('expire', 0, 'user') != 0) {
            if ((time()-$this->Users->expireDate($verify_uri)) > (Arag_Config::get('expire', 0, 'user') * 3600)) {
                $error_message = _("This uri is expired! Please contact site administrator for further information.");
                $show_form     = false;            
            }
        }

        $data = array('error_message' => $error_message,
                      'show_form'     => $show_form,
                      'uri'           => $verify_uri);
        $this->layout->content = new View('frontend/verify', $data);
    }
    // }}}
    // {{{ index_write
    public function index_write()
    {
        $show_form  = true;
        $verify_uri = $this->input->post('uri');
        $username   = $this->input->post('username');
        $password   = $this->input->post('password');

        if ($this->Users->checkVerify($username, $password, $verify_uri, $status, Arag_Config::get('verify_block_expire', 0.5) * 3600)) {
            $this->Users->verify($username, $password, $verify_uri);
            $this->Users->blockUser($username);
            $this->layout->content = new View('frontend/verified');
                    
        } else {
            
            // Shit, you missed!
            if ($status === Users_Model::USER_NOT_FOUND) {
                $error_message[] = _("Wrong Username or Password.");
                if (Arag_Config::get('verify_block_counter', 3) != 0) {
                    $error = _("Attention!! Your url will be removed and your username will get blocked if you miss %s% times while verifying!");
                    $error_message[] = str_replace('%s%', Arag_Config::get('block_counter', 3), $error);
                }

            }

            if ($status & Users_Model::USER_INCORRECT_PASS) {
                $error_message[] = _("Wrong Username or Password."); 
                $block_info = $this->Users->getBlockInfo($username);

                if (Arag_Config::get('verify_block_counter', 3) != 0 && $block_info->block_counter >= Arag_Config::get('verify_block_counter', 3)) {
                    if (Arag_Config::get('verify_block_action', 6) & MultiSite_Model::BLOCK) {
                        $this->Users->blockUser($username, 1, 0, time());
                    }

                    if (Arag_Config::get('verify_block_action', 6) & MultiSite_Model::URI) {
                        $this->Users->changePassword($username, NULL);
                    }

                    $show_form = false;

                    $error_message[] = _("Attention!! Your url removed and your username got blocked! Contact site administrator for further information");

                } else {
                    $this->Users->blockUser($username, 0, ++$block_info->block_counter);
                    if (Arag_Config::get('verify_block_counter', 3) != 0) {
                        $error = _("Attention!! Your url will be removed and your username will get blocked if you miss %s% times while verifying!");
                        $error_message[] = str_replace('%s%', Arag_Config::get('block_counter', 3), $error);
                    }

                }
            }

            if ($status & Users_Model::USER_BLOCKED) {
                $block_info      = $this->Users->getBlockInfo($username);
                if ($block_info->block_date > 0) {
                    $error           = _("This user name is blocked. Please contact site administrator for further information or wait for %h% hours and %m% minutes");
                    $waiting_hours   = floor((((Arag_Config::get('verify_block_expire', 0.5) * 3600) + $block_info->block_date) - time()) / 3600);
                    $waiting_mins    = floor(((((Arag_Config::get('verify_block_expire', 0.5) * 3600) + $block_info->block_date) - time()) % 3600) / 60);
                    $error_message[] = str_replace(array('%h%', '%m%'), array($waiting_hours, $waiting_mins) , $error);                   

                } else {
                    $error_message[] = _("This user name is blocked. Please contact site administrator for further information."); 

                }
            }

            $error_message = implode("\n", $error_message); 

            $data = array('error_message' => $error_message,
                          'show_form'     => $show_form,
                          'uri'           => $verify_uri);

            $this->layout->content = new View('frontend/verify', $data);
        }
    }
    // }}}
}
?>