<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

require_once Arag::find_file('oauth', 'vendor', 'oauth-php/OAuthStore', True);

class Backend_Controller extends Controller
{
    // {{{ Constructor
    public function __construct()
    {
        parent::__construct();

        // Default page title
        $this->layout->page_title = 'OAuth';

        // Validation messages
        $this->validation->message('required', _("%s is required."));
        $this->validation->message('numeric', _("%s should be numeric."));
        $this->validation->message('standard_text', _("%s should be ordinary text."));
        $this->validation->message('aplpha_numeric', _("%s should be alpha numeric."));
        $this->validation->message('url', _("%s should be a valid url."));
        $this->validation->message('email', _("Please enter a valid email address"));

        // Global tabbedbock
        $this->global_tabs = new TabbedBlock_Component('global_tabs');
        $this->global_tabs->setTitle(_("OAuth"));
        $this->global_tabs->addItem(_("Register Consumers"), 'oauth/backend/consumers');
        $this->global_tabs->addItem(_("Consumers List"), 'oauth/backend/consumers', 'oauth/backend/consumers');
        $this->global_tabs->addItem(_("Register New Consumer"), 'oauth/backend/register', 'oauth/backend/consumers');

        $connection  = parse_url(Kohana::config('database.default.connection'));
        $this->store = OAuthStore::instance('MySQL', Array('server' => $connection['host'], 'password' => $connection['pass'],
                                                           'username' => $connection['user'], 'database' => ltrim($connection['path'], '/')));
    }
    // }}}
    // {{{ consumers
    public function consumers()
    {
        $consumers = new PList_Component('consumers');
        $consumers->setResource($this->store->listConsumers(1));
        $consumers->setLimit(Arag_Config::get('limit', 0));
        $consumers->addColumn('id', _("User ID"));
        $consumers->addColumn('requester_name', _("Requester Name"));
        $consumers->addColumn('requester_email', _("Requester Email"));
        $consumers->addColumn('consumer_key', _("Consumer Key"));
        $consumers->addColumn('consumer_secret', _("Consumer Secret"));
        $consumers->addColumn('application_uri', _("Application URI"));
        $consumers->addColumn('application_title', _("Application Title"));
        $consumers->addAction('oauth/backend/edit/#consumer_key#/#user_id#', _("Edit"), 'edit_action');
        $consumers->addAction('oauth/backend/delete/#consumer_key#/#user_id#', _("Delete"), 'delete_action');

        $consumers->setEmptyListMessage(_("There is no entry!"));

        $this->layout->content = new View('backend/consumer');
    }
    // }}}
    // {{{ register
    // {{{ register_read
    public function register_read()
    {
        $this->layout->content         = new View('backend/consumer_form');
        $this->layout->content->uri    = 'oauth/backend/register';
        $this->layout->content->action = 'register';
    }
    // }}}
    // {{{ register_validate_write
    public function register_validate_write()
    {
        $this->validation->name('requester_name', _("Requester Name"))->add_rules('requester_name', 'required', 'standard_text');
        $this->validation->name('requester_email', _("Requester Email"))->add_rules('requester_email', 'required', 'email');
        $this->validation->name('callback_uri', _("Callback URI"))->add_rules('callback_uri', 'url');
        $this->validation->name('application_uri', _("Application URI"))->add_rules('application_uri', 'url');
        $this->validation->name('application_title', _("Application Title"))->add_rules('application_title', 'standard_text');
        $this->validation->name('application_descr', _("Application Descr"))->add_rules('application_descr', 'standard_text');
        $this->validation->name('application_notes', _("Application Notes"))->add_rules('application_notes', 'standard_text');
        $this->validation->name('application_type', _("Application Type"))->add_rules('application_type', 'alpha_numeric');
        $this->validation->name('application_commercial', _("application_commercial"))->add_rules('application_commercial', 'numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ register_write_error
    public function register_write_error()
    {
        return $this->register_read();
    }
    // }}}
    // {{{ register_write
    public function register_write()
    {
        $user_id  = 1;
        $consumer = $this->input->post();

        // Register the consumer
        $key      = $this->store->updateConsumer($consumer, $user_id);
        $consumer = $this->store->getConsumer($key, $user_id);

        // TODO: this should not be here, we have to implement a backend for client, to
        // register/update/delete server for a specified consumer

        // The server description
        $server = array(
            'consumer_key'      => $consumer['consumer_key'],
            'consumer_secret'   => $consumer['consumer_secret'],
            'server_uri'        => Kohana::config('oauth.api'),
            'request_token_uri' => url::site('oauth/frontend/server/request_token'),
            'authorize_uri'     => url::site('oauth/frontend/server/authorize'),
            'access_token_uri'  => url::site('oauth/frontend/server/access_token'),
            'signature_methods' => array('HMAC-SHA1', 'PLAINTEXT')
        );

        // Save the server in the the OAuthStore
        $this->store->updateServer($server, $user_id);

        url::redirect('oauth/backend/consumers');
    }
    // }}}
    // }}}
    // {{{ delete
    // {{{ delete_read
    public function delete_read($consumer_key, $user_id)
    {
        $this->global_tabs->setParameter('consumer_key', $consumer_key);
        $this->global_tabs->setParameter('user_id', $user_id);
        $this->global_tabs->addItem(_("Delete Consumer"), 'oauth/backend/delete/%consumer_key%/%user_id%', 'oauth/backend/consumers');

        $consumer = $this->store->getConsumer($consumer_key, $user_id);
        $data     = Array('user_id'      => $user_id,
                          'consumer_key' => $consumer_key,
                          'requester'    => $consumer['requester_name'].' ('.$consumer['requester_email'].')');

        $this->layout->content = new View('backend/delete', $data);
    }
    // }}}
    // {{{ delete_validate_read
    public function delete_validate_read()
    {
        $this->validation->name(0, _("Consumer Key"))->add_rules(0, 'required', 'valid::alpha_numeric');
        $this->validation->name(1, _("User ID"))->add_rules(1, 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_read_error
    public function delete_read_error()
    {
        $this->_invalid_request('oauth/backend/consumers', _("Invalid ID"));
    }
    // }}}
    // {{{ delete_write
    public function delete_write()
    {
        $this->store->deleteConsumer($this->input->post('consumer_key'), $this->input->post('user_id'));

        url::redirect('oauth/backend/consumers');
    }
    // }}}
    // {{{ delete_validate_write
    public function delete_validate_write()
    {
        $this->validation->name('consumer_key', _("Consumer Key"))->add_rules('consumer_key', 'required', 'valid::alpha_numeric');
        $this->validation->name('user_id', _("User ID"))->add_rules('user_id', 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ delete_write_error
    public function delete_write_error()
    {
        $this->_invalid_request('oauth/backend/consumers', _("Invalid ID"));
    }
    // }}}
    // }}}
    // {{{ edit
    // {{{ edit_read
    public function edit_read($consumer_key, $user_id)
    {
        $this->global_tabs->setParameter('consumer_key', $consumer_key);
        $this->global_tabs->setParameter('user_id', $user_id);
        $this->global_tabs->addItem(_("Edit Consumer"), 'oauth/backend/edit/%consumer_key%/%user_id%', 'oauth/backend/consumers');

        $consumer = $this->store->getConsumer($consumer_key, $user_id);

        $this->layout->content         = new View('backend/consumer_form', $consumer);
        $this->layout->content->uri    = 'oauth/backend/edit';
        $this->layout->content->action = 'edit';
    }
    // }}}
    // {{{ edit_validate_read
    public function edit_validate_read()
    {
        $this->validation->name(0, _("Consumer Key"))->add_rules(0, 'required', 'valid::alpha_numeric');
        $this->validation->name(1, _("User ID"))->add_rules(1, 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_read_error
    public function edit_read_error()
    {
        $this->_invalid_request('oauth/backend/consumers', _("Invalid ID"));
    }
    // }}}
    // {{{ edit_write
    public function edit_write()
    {
        $user_id  = 1;
        $consumer = $this->input->post();

        // Register the consumer
        $this->store->updateConsumer($consumer, $user_id);

        url::redirect('oauth/backend/consumers');
    }
    // }}}
    // {{{ edit_validate_write
    public function edit_validate_write()
    {
        $this->validation->name('requester_name', _("Requester Name"))->add_rules('requester_name', 'required', 'standard_text');
        $this->validation->name('requester_email', _("Requester Email"))->add_rules('requester_email', 'required', 'email');
        $this->validation->name('callback_uri', _("Callback URI"))->add_rules('callback_uri', 'url');
        $this->validation->name('application_uri', _("Application URI"))->add_rules('application_uri', 'url');
        $this->validation->name('application_title', _("Application Title"))->add_rules('application_title', 'standard_text');
        $this->validation->name('application_descr', _("Application Descr"))->add_rules('application_descr', 'standard_text');
        $this->validation->name('application_notes', _("Application Notes"))->add_rules('application_notes', 'standard_text');
        $this->validation->name('application_type', _("Application Type"))->add_rules('application_type', 'alpha_numeric');
        $this->validation->name('application_commercial', _("application_commercial"))->add_rules('application_commercial', 'numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ edit_write_error
    public function edit_write_error()
    {
        $this->global_tabs->setParameter('consumer_key', $this->input->post('consumer_key'));
        $this->global_tabs->setParameter('user_id', $this->input->post('user_id'));
        $this->global_tabs->addItem(_("Edit Consumer"), 'oauth/backend/edit/%consumer_key%/%user_id%', 'oauth/backend/consumers');

        $this->layout->content         = new View('backend/consumer_form', $this->input->post());
        $this->layout->content->uri    = 'oauth/backend/edit';
        $this->layout->content->action = 'edit';
    }
    // }}}
    // }}}
}
