<?php
/**
 * Redirect plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();


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

        if($ACT != 'show') return;

        $redirects = confToHash(dirname(__FILE__) . '/redirect.conf');

        if($INPUT->get->str('redirect') == 'no') {
            // return if redirection is temporarily disabled by url parameter
            return;
        } else {
            if($redirects[$ID]) {
                if(preg_match('/^https?:\/\//', $redirects[$ID])) {
                    send_redirect($redirects[$ID]);
                } else {
                    if($this->getConf('showmsg')) {
                        msg(sprintf($this->getLang('redirected'), hsc($ID)));
                    }
                    $link = explode('#', $redirects[$ID], 2);
                    send_redirect(wl($link[0], '', true) . '#' . rawurlencode($link[1]));
                }
                exit;
            }
        }
    }

}

