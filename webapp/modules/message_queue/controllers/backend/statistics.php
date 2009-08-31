<?php
// vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker:
// +-------------------------------------------------------------------------+
// | Author: Armen Baghumian <armen@OpenSourceClub.org>                      |
// +-------------------------------------------------------------------------+
// $Id$
// ---------------------------------------------------------------------------

class Statistics_Controller extends MessageQueue_Backend
{
    // {{{ Properties

    protected $server_storage;
    protected $client_storage;

    // }}}
    // {{{ __construct
    public function __construct()
    {
        parent::__construct();

        require Arag::find_file('message_queue', 'vendor', 'dropr/classes/dropr', True);

        $server_storage_type  = Kohana::config('dropr.server_storage_type');
        $server_storage_dsn   = Kohana::config('dropr.server_storage_dsn');
        $this->server_storage = dropr_Server_Storage_Abstract::factory($server_storage_type, $server_storage_dsn);

        $client_storage_type  = Kohana::config('dropr.client_storage_type');
        $client_storage_dsn   = Kohana::config('dropr.client_storage_dsn');
        $this->client_storage = dropr_Client_Storage_Abstract::factory($client_storage_type, $client_storage_dsn);

        $channels = $this->server_storage->getQueuedChannels();
        asort($channels);

        foreach ($channels as $channel) {
            $this->global_tabs->addItem(_(ucfirst($channel)), 'message_queue/backend/statistics/index/'.$channel);
        }

        $this->global_tabs->addItem(_("Dropr"), 'message_queue/backend/statistics/index/dropr');
    }
    // }}}
    // {{{ index_any
    public function index_any($channel)
    {
        $this->layout->content = new View('backend/statistics');

        if ($channel === 'dropr') {
            $queued    = $this->client_storage->countQueuedMessages();
            $sent      = $this->client_storage->countSentMessages();
            $messages  = $this->client_storage->getQueuedMessages(Null, $blackList = Array());
            $entries   = Array();

            foreach ($messages as $transporters) {
                foreach ($transporters as $key => $message) {
                    $entries[$key]['priority'] = $message->getPriority();
                    $entries[$key]['channel']  = $message->getChannel();
                }
            }

            $this->layout->content->sent = $sent;

        } else {
            $queued    = $this->server_storage->countQueuedMessages($channel);
            $processed = $this->server_storage->countProcessedMessages($channel);
            $messages  = $this->server_storage->getMessages($channel);
            $entries   = Array();

            foreach ($messages as $key => $message) {
                $entries[$key]['priority'] = $message->getPriority();
                $entries[$key]['state']    = $message->getState();
                $entries[$key]['time']     = format::date($message->getTime());
                $entries[$key]['client']   = $message->getClient();
            }

            $this->layout->content->processed = $processed;
        }

        $columns = empty($entries) ? Array() : array_keys(current($entries));

        $messages = new PList_Component('messages');
        $messages->setResource($entries);
        $messages->setLimit(Arag_Config::get('limit', 0));

        foreach ($columns as $column) {
            $messages->addColumn($column, _(ucfirst($column)));
        }

        $this->layout->content->queued  = $queued;
        $this->layout->content->entries = $entries;
        $this->layout->content->channel = $channel;
    }
    // }}}
    // {{{ wipe
    // {{{ wipe_write
    public function wipe_write()
    {
        $channel    = $this->input->post('channel');
        $older_than = $this->input->post('older_than');

        if ($channel == 'dropr') {
            $this->client_storage->wipeSentMessages($older_than);
        } else {
            $this->server_storage->wipeSentMessages($older_than, $channel);
        }

        url::redirect('message_queue/backend/statistics/index/'.$channel);
    }
    // }}}
    // {{{ wipe_validate_write
    public function wipe_validate_write()
    {
        $this->validation->name('channel', _("Channel"))->add_rules('channel', 'required');
        $this->validation->name('older_than', _("Older than"))->add_rules('older_than', 'required', 'valid::numeric');

        return $this->validation->validate();
    }
    // }}}
    // {{{ wipe_write_error
    public function wipe_write_error()
    {
        $this->index_any($this->input->post('channel'));
    }
    // }}}
    // }}}
}
