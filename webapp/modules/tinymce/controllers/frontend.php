<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Frontend_Controller extends Controller
{
    function index_read()
    {
        $this->layout = new View('easyUpload');
    }

    function index_write()
    {

        $file                   = $this->input->post('file'); //uploaded file
        $name                   = $this->input->post('name'); //RTE name
        $extension              = pathinfo($file['name'], PATHINFO_EXTENSION);
        $module                 = $this->session->get('rte_'.$name); //this session is set by {arag_rte} smarty plugin
        $moduleDirectory        = 'modpub/'.$module;
        $attachments            = $moduleDirectory.'/attachments';

        $attachmentsUrl         = url::base().'/'.$attachments;
        $attachmentsPath        = DOCROOT.'/'.$attachments;

        $modulePath             = DOCROOT.'/'.$attachments;

        if (!is_uploaded_file($file['tmp_name'])) {
          //TODO
        }

        if (!file_exists($modulePath)) {
          mkdir($modulePath);
        }

        if (!file_exists($attachmentsPath)) {
          mkdir($attachmentsPath);
        }

        $filename = md5($file['name'].'_'.time()).'.'.$extension;
        $i        = 0;

        while(file_exists($filename)) {
          $filename = '('.$i.') '.$filename;
        }
        $result = move_uploaded_file($file['tmp_name'], $attachmentsPath.'/'.$filename);

        $this->layout->content      = new View('result');
        $this->layout->content->url = $attachmentsUrl.'/'.$filename;
    }
}