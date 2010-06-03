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

        $filename       = md5($file['name'].'_'.time()).'.'.$extension;
        $filename_thumb = md5($file['name'].'_'.time()).'-thumb.'.$extension;
        $i              = 0;
        $size           = Arag_Config::get('tiny_mce.size', Array(), $module, True);

        while(file_exists($attachmentsPath.'/'.$filename)) {
            $filename = '('.$i.') '.$filename;
            $i++;
        }

        while(file_exists($attachmentsPath.'/'.$filename_thumb)) {
            $filename_thumb = '('.$i.') '.$filename_thumb;
            $i++;
        }

        $result = move_uploaded_file($file['tmp_name'], $attachmentsPath.'/'.$filename);

        $thumb = False;

        if (!empty($size)) {
            $image     = New Image($attachmentsPath.'/'.$filename);
            $imagesize = array('width' => $image->__get('width'), 'height' => $image->__get('height'));

            if (isset($size['width']) && $size['width'] < $imagesize['width']) {
                $image->resize($size['width'], $imagesize['height'], Image::WIDTH);
                $thumb = True;

                $imagesize = array('width' => $image->__get('width'), 'height' => $image->__get('height'));
            }

            if (isset($size['height']) && $size['height'] < $imagesize['height']) {
                $image->resize($imagesize['width'], $size['height'], Image::HEIGHT);
                $thumb = True;
            }

            $thumb && $image->save($attachmentsPath.'/'.$filename_thumb);
        }

        $this->layout->content            = new View('result');
        $this->layout->content->url       = $attachmentsUrl.'/'.$filename;
        $this->layout->content->url_thumb = $attachmentsUrl.'/'.$filename_thumb;
        $this->layout->content->thumb     = $thumb;
    }
}
