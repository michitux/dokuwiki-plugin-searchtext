<?php
/**
 * SearchText Action Plugin
 * 
 * @author     Todd Augsburger, Michael Hamann <todd@rollerorgans.com>
 */
 
if(!defined('DOKU_INC')) die();

class action_plugin_searchtext extends DokuWiki_Action_Plugin {
  function register(Doku_Event_Handler $controller) {
    $controller->register_hook('INDEXER_PAGE_ADD', 'BEFORE',  $this, '_getSearch');
    $controller->register_hook('FULLTEXT_SNIPPET_CREATE', 'BEFORE',  $this, '_getSnippet');
    $controller->register_hook('INDEXER_VERSION_GET', 'BEFORE', $this, '_indexerVersion');
  }

  function _indexerVersion(&$event, $param) {
    $event->data['plugin_searchtext'] = '1-' . $this->getConf('search_in_text');
  }
 
  function _getSearch(&$event, $param) {
    if($this->getConf('search_in_text')) {
      global $ID;

      $prevID = $ID;
      $ID = $event->data['page'];
      $event->data['body'] .= p_cached_output(wikiFN($event->data['page']),'text');
      $ID = $prevID;
      $event->preventDefault();
    }
  }
 
  function _getSnippet(&$event, $param) {
    if($this->getConf('snippet_in_text')) {
      global $ID;

      $prevID = $ID;
      $ID = $event->data['id'];
      $event->data['text'] = p_cached_output(wikiFN($event->data['id']),'text');
      $ID = $prevID;
    }
  }
}
