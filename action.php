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

    var $redirects;

    function __construct() {
        global $config_cascade;
        $config_cascade = array_merge( $config_cascade, array(
            'redirects' => array(
                'default' => array(DOKU_CONF.'redirect.conf'),
                'local'   => array(DOKU_CONF.'redirect.local.conf'),
            ),
        ));
        $this->redirects = retrieveConfig('redirects','confToHash');
    }

    /**
     * register the eventhandlers
     */
    function register(&$controller){
        $controller->register_hook('DOKUWIKI_STARTED',
                                   'AFTER',
                                   $this,
                                   'handle_start',
                                   array());
    }

    /**
     * handle event
     */
    function handle_start(&$event, $param){
        global $ID, $ACT;

        if($ACT != 'show') return;

        if($this->redirects[$ID]){
            if(preg_match('/^https?:\/\//',$this->redirects[$ID])){
                send_redirect($this->redirects[$ID]);
            }else{
                if($this->getConf('showmsg')){
                    msg(sprintf($this->getLang('redirected'),hsc($ID)));
                }
                $link = explode('#', $this->redirects[$ID], 2);
                send_redirect(wl($link[0] ,'',true) . '#' . rawurlencode($link[1]));
            }
            exit;
        }
    }


}

