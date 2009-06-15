<?php
// +-------------------------------------------------------------------------+
// | Author: Jila Khaghani <jilakhaghani@gmail.com>                          |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------
class  Logger_Backend extends Controller
{
    // {{{
    function __construct()
    {
        parent::__construct();

        //Load the Model
        $this->logger = new Logger_Model();

        $this->validation->message('required',_("%s is required"));

        // Load logs config
        $this->messages = Arag_Config::get('logger.messages', Null, 'logger', True);

        foreach ($this->messages as &$message) {
            $message = _($message);
        }

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("Logs"));
        $this->global_tabs->addItem(_("List"), "logger/backend");
    }
}
