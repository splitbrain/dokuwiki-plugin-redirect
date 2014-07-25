<?php
/**
 * Redirect plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_redirect extends DokuWiki_Action_Plugin {

    protected $ConfFile;  // path/to/redirection config file

    public function __construct() {
        $this->ConfFile = DOKU_CONF.'redirect.conf';
    }

    /**
     * register the eventhandlers
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'handle_start', array());
    }

    /**
     * handle event
     */
    public function handle_start(&$event, $param){
        global $ID, $ACT;

        if ($ACT != 'show') return;

        $redirects = confToHash($this->ConfFile);
        if ($redirects[$ID]) {
            if (preg_match('/^https?:\/\//',$redirects[$ID])) {
                send_redirect($redirects[$ID]);
            } else {
                if ($this->getConf('showmsg')) {
                    msg(sprintf($this->getLang('redirected'),hsc($ID)));
                }
                $link = explode('#', $redirects[$ID], 2);
                send_redirect(wl($link[0] ,'',true) . '#' . rawurlencode($link[1]));
            }
            exit;
        }
    }
}
