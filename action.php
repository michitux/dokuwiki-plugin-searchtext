<?php
/**
 * SearchText Action Plugin
 * 
 * @author     Todd Augsburger <todd@rollerorgans.com>
 */
 
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');
require_once(DOKU_INC.'inc/parserutils.php');

 
class action_plugin_searchtext extends DokuWiki_Action_Plugin {
 
  /**
   * return some info
   */
  function getInfo(){
    return array(
		 'author' => 'Todd Augsburger',
		 'email'  => 'todd@rollerorgans.com',
		 'date'   => '2008-02-26',
		 'name'   => 'SearchText (action plugin)',
		 'desc'   => "Searches pages rendered as text instead of raw wiki.\nDisplays search result snippets from text.\n(Configuration is in Admin Configuration Settings)\n\nNOTE: Requires a text renderer plugin such as the one available at http://cobs.rollerorgans.com/plugins/text.zip (see: http://wiki.splitbrain.org/plugin:text)\n\nNOTE: This plugin REQUIRES modification (to add an event) at the beginning of the idx_getPageWords() function in inc/fulltext.php :\n-    \$text     = rawWiki(\$id);\n+    \$text = trigger_event('FULLTEXT_SNIPPET_GETTEXT', \$id, 'rawWiki');\n\n",
		 'url'    => 'http://wiki.splitbrain.org/plugin:searchtext',
		 );
  }
 
  function register(&$controller) {
    $controller->register_hook('INDEXER_PAGE_ADD', 'BEFORE',  $this, '_getSearch');
    $controller->register_hook('FULLTEXT_SNIPPET_GETTEXT', 'BEFORE',  $this, '_getSnippet');
  }
 
  function _getSearch(&$event, $param) {
    if($this->getConf('search_in_text')) {
      global $ID;

      $prevID = $ID;
      $ID = $event->data;
      $event->data[1] .= p_cached_output(wikiFN($event->data[0]),'text');
      $ID = $prevID;
      $event->preventDefault();
    }
  }
 
  function _getSnippet(&$event, $param) {
    if($this->getConf('snippet_in_text')) {
      global $ID;

      $prevID = $ID;
      $ID = $event->data;
      $event->result = p_cached_output(wikiFN($event->data),'text');
      $ID = $prevID;
      $event->preventDefault();
    }
  }
}