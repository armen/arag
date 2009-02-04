<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Emil Sedgh <emilsedgh@gmail.com>                                |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Comments_Controller extends Controller
{
    // {{{ comment_add_write
    public function comment_add_write()
    {
        $key          = $this->input->post('key');
        $namespace    = $this->session->get('comment.'.$key.'.namespace');
        $reference_id = $this->session->get('comment.'.$key.'.reference_id');
        $uri          = $this->session->get('comment.'.$key.'.uri');
        $controller   = $this->session->get('comment.'.$key.'.controller');
        $comment      = $this->input->post('comment');
        $attachments  = $this->input->post('attachments');

        if (!$namespace || !$reference_id || !$uri) {
          $this->_invalid_request();
        }

        $Comment       = Model::load('Comment', 'comment');
        $attach_string = '';

        if (is_array($attachments)) {

            $upload_path = DOCROOT.'modpub/comment/attachments/';
            $upload_url  = url::site($controller.'/get_comment_file');

            if (is_writable($upload_path) && file_exists($upload_path)) {

                $count = 0;

                foreach($attachments as $index=>$attachment) {

                    if ($attachment['error'] === 0) {

                        $filename = sha1(rand(10000, 99999).microtime()).'.'.file::extension($attachment['name']);
                        $attach_string .= '<li><a href='.$upload_url.'/'.$filename.'>'._("File ").($index+1).'</a></li>';
                        move_uploaded_file($attachment['tmp_name'], $upload_path.$filename);
                        $count++;
                    }
                }

                if ($count) {
                    $attach_string  = "\n\n"._("Attachments:").'<ul>'.$attach_string;
                    $attach_string .= '</ul>';
                    $comment .= $attach_string;
                }

            } else {

                $this->session->set_flash('error', _("Cannot upload attachment(s). Please contact website's administrator"));
                Kohana::log('error', $upload_path.' is not writable or doesnt exists');
            }
        }

        $Users   = Model::load('Users', 'user');
        $profile = $Users->getUser($this->session->get('user.username'));

        $Comment->createComment($namespace, $reference_id, $this->session->get('user.username'), $comment, 0, 0, $profile['name']);
        url::redirect($uri);
    }
    // }}}
    // {{{ order_validate_write
    public function comment_add_validate_write()
    {
        $this->validation->name('comment', _("Comment"))->add_rules('comment', 'required')->post_filter('security::xss_clean','comment');
        $this->validation->name('attachments.*', _("Attachments"))->add_rules('attachments.*',
                                                                              'upload::type['.Kohana::config('config.allowed_extensions').']');

        return $this->validation->validate();
    }
    // }}}
    // {{{ comment_add_write_error
    public function comment_add_write_error()
    {
        $this->_invalid_request($this->session->get('comment.reference_id'));
    }
    // }}}
// THIS IS HELL OF A TODO! MUST BE FIXED! ATTACHMENTS ARE NOT WPRKING!
//     // {{{ get_comment_file
//     public function get_comment_file($filename)
//     {
//         $path = DOCROOT.'modpub/comment/attachments/'.$filename;
//
//         if (!file_exists($path)) {
//             return false;
//         }
//
//         $id = (int) current(explode('_', $filename));
//
//         $this->_get_file($path);
//     }
    // }}}
    // {{{ _get_file
    private function _get_file($path)
    {
        ob_clean();
        header('Content-Type:'.file::mime($path));
        print file_get_contents($path);
    }
    // }}}
}
