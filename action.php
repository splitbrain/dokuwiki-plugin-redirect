<?php
/**
 * Login/Logout logging plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_redirect extends DokuWiki_Action_Plugin {

    var $islogin = false;

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
        global $ID;
        global $ACT;

        if($ACT != 'show') return;

        $redirects = confToHash(dirname(__FILE__).'/redirect.conf');
        if($redirects[$ID]){
            if(preg_match('/^https?:\/\//',$redirects[$ID])){
                header('Location: '.$redirects[$ID]);
            }else{
                header('Location: '.wl($redirects[$ID] ,'',true));
            }
            exit;
        }
    }


}

