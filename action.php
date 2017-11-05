<?php
/**
 * Redirect plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Class action_plugin_redirect
 *
 * Execute redirects
 */
class action_plugin_redirect extends DokuWiki_Action_Plugin {

    /**
     * register the eventhandlers
     *
     * @param Doku_Event_Handler $controller
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook(
            'DOKUWIKI_STARTED',
            'AFTER',
            $this,
            'handle_start',
            array()
        );
    }

    /**
     * handle event
     *
     * @param Doku_Event $event
     * @param array $param
     */
    public function handle_start(Doku_Event $event, $param) {
        global $ID;
        global $ACT;
        global $INPUT;

        if($ACT != 'show') return;
        if($INPUT->get->str('redirect') == 'no') return;

        /** @var helper_plugin_redirect $hlp */
        $hlp = plugin_load('helper', 'redirect');
        $url = $hlp->getRedirectURL($ID);
        if($url) send_redirect($url);
    }

}

