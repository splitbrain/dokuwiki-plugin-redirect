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
 */
class action_plugin_redirect extends DokuWiki_Action_Plugin {

    /**
     * register the eventhandlers
     *
     * @param Doku_Event_Handler $controller
     */
    function register(Doku_Event_Handler $controller) {
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
    function handle_start(Doku_Event $event, $param) {
        global $ID;
        global $ACT;
        global $INPUT;

        // abort early
        if($ACT != 'show') return;
        if($INPUT->get->str('redirect') == 'no') return;
        $redirects = confToHash(dirname(__FILE__) . '/redirect.conf');
        if(empty($redirects[$ID])) return;

        // construct target URL
        if(preg_match('/^https?:\/\//', $redirects[$ID])) {
            $url = $redirects[$ID];
        } else {
            if($this->getConf('showmsg')) {
                msg(sprintf($this->getLang('redirected'), hsc($ID)));
            }
            $link = explode('#', $redirects[$ID], 2);
            $url = wl($link[0], '', true, '&');
            if(isset($link[1])) $url .= '#' . rawurlencode($link[1]);
        }

        // redirect
        send_redirect($url);
    }

}

