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
 * Class helper_plugin_redirect
 *
 * Save and load the config file
 */
class helper_plugin_redirect extends DokuWiki_Admin_Plugin {

    const CONFIG_FILE = DOKU_CONF . '/redirect.conf';
    const LEGACY_FILE = __DIR__ . '/redirect.conf';

    /**
     * helper_plugin_redirect constructor.
     *
     * handles the legacy file
     */
    public function __construct() {
        // move config from plugin directory to conf directory
        if(!file_exists(self::CONFIG_FILE) &&
            file_exists(self::LEGACY_FILE)) {
            rename(self::LEGACY_FILE, self::CONFIG_FILE);
        }
    }

    /**
     * Saves the config file
     *
     * @param string $config the raw text for the config
     * @return bool
     */
    public function saveConfigFile($config) {
        return io_saveFile(self::CONFIG_FILE, cleanText($config));
    }

    /**
     * Load the config file
     *
     * @return string the raw text of the config
     */
    public function loadConfigFile() {
        if(!file_exists(self::CONFIG_FILE)) return '';
        return io_readFile(self::CONFIG_FILE);
    }

    /**
     * Get the redirect URL for a given ID
     *
     * Handles conf['showmsg']
     *
     * @param string $id the ID for which the redirect is wanted
     * @return bool|string the full URL to redirect to
     */
    public function getRedirectURL($id) {
        $redirects = confToHash(self::CONFIG_FILE);
        if(empty($redirects[$id])) return false;

        if(preg_match('/^https?:\/\//', $redirects[$id])) {
            $url = $redirects[$id];
        } else {
            if($this->getConf('showmsg')) {
                msg(sprintf($this->getLang('redirected'), hsc($id)));
            }
            $link = explode('#', $redirects[$id], 2);
            $url = wl($link[0], '', true, '&');
            if(isset($link[1])) $url .= '#' . rawurlencode($link[1]);
        }

        return $url;
    }
}
