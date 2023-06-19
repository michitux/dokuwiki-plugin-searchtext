<?php

/**
 * SearchText Action Plugin
 *
 * @author     Michael Hamann <michael@content-space.de>
 * @author     Todd Augsburger <todd@rollerorgans.com>
 */
class action_plugin_searchtext extends DokuWiki_Action_Plugin
{
    /** @inheritdoc */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('INDEXER_PAGE_ADD', 'BEFORE', $this, 'getSearch');
        $controller->register_hook('FULLTEXT_SNIPPET_CREATE', 'BEFORE', $this, 'getSnippet');
        $controller->register_hook('INDEXER_VERSION_GET', 'BEFORE', $this, 'indexerVersion');
    }

    /**
     * @param Doku_Event $event INDEXER_VERSION_GET
     * @return void
     */
    public function indexerVersion(Doku_Event $event)
    {
        if (!$this->getConf('search_in_text')) return;
        /** @var renderer_plugin_text $text_plugin */
        $text_plugin = plugin_load('renderer', 'text');
        if ($text_plugin === null) return;
        $text_info = $text_plugin->getInfo();
        $event->data['plugin_searchtext'] = '1-' . $text_info['date'];
    }

    /**
     * @param Doku_Event $event INDEXER_PAGE_ADD
     * @return void
     */
    public function getSearch(Doku_Event $event)
    {
        global $ID;
        if (!$this->getConf('search_in_text')) return;
        $prevID = $ID;
        $ID = $event->data['page'];
        $event->data['body'] .= p_cached_output(wikiFN($event->data['page']), 'text');
        $ID = $prevID;
        $event->preventDefault();

    }

    /**
     * @param Doku_Event $event FULLTEXT_SNIPPET_CREATE
     * @return void
     */
    public function getSnippet(Doku_Event $event)
    {
        global $ID;
        if (!$this->getConf('snippet_in_text')) return;
        $prevID = $ID;
        $ID = $event->data['id'];
        $event->data['text'] = p_cached_output(wikiFN($event->data['id']), 'text');
        $ID = $prevID;
    }
}
