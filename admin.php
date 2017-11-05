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
 * Class admin_plugin_redirect
 *
 * Provide editing mechanism for configuration
 */
class admin_plugin_redirect extends DokuWiki_Admin_Plugin {

    /** @var helper_plugin_redirect */
    protected $hlp;

    /**
     * admin_plugin_redirect constructor.
     */
    public function __construct() {
        $this->hlp = plugin_load('helper', 'redirect');
    }

    /**
     * Access for managers allowed
     */
    public function forAdminOnly() {
        return false;
    }

    /**
     * return sort order for position in admin menu
     */
    public function getMenuSort() {
        return 140;
    }

    /**
     * return prompt for admin menu
     */
    public function getMenuText($language) {
        return $this->getLang('name');
    }

    /**
     * handle user request
     */
    public function handle() {
        global $INPUT;
        if($INPUT->post->has('redirdata')) {
            if($this->hlp->saveConfigFile($INPUT->post->str('redirdata'))) {
                msg($this->getLang('saved'), 1);
            }
        }
    }

    /**
     * output appropriate html
     */
    public function html() {
        global $lang;
        echo $this->locale_xhtml('intro');
        echo '<form action="" method="post" >';
        echo '<input type="hidden" name="do" value="admin" />';
        echo '<input type="hidden" name="page" value="redirect" />';
        echo '<textarea class="edit" rows="15" cols="80" style="height: 300px" name="redirdata">';
        echo formtext($this->hlp->loadConfigFile());
        echo '</textarea><br />';
        echo '<input type="submit" value="' . $lang['btn_save'] . '" class="button" />';
        echo '</form>';
    }

}
