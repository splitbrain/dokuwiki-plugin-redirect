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

        $rules = explode('|', $redirects[$id]);
        foreach ($rules as $rule) {
            //search for conditional redirect
            list($redirect, $condition) = explode(' ?', $rule, 2);
            if ($condition && !self::executeCondition($condition)) continue;

            $redirect = trim($redirect);
            if(preg_match('/^https?:\/\//', $redirect)) {
                $url = $redirects[$id];
            } else {
                if($this->getConf('showmsg')) {
                    msg(sprintf($this->getLang('redirected'), hsc($id)));
                }
                $link = explode('#', $redirect, 2);
                $url = wl($link[0], '', true, '&');
                if(isset($link[1])) $url .= '#' . rawurlencode($link[1]);
            }

            return $url;
        }

        return false;
    }

    /**
     * Executes a redirect condition and returns its result
     *
     * @param string $condition the condition to execute
     *
     * @return bool true if condition is met
     */
    public function executeCondition($condition) {
        global $INFO;

        $operators = ['='];
        $variables = [
            '$USER$' => $INFO['client'],
            '$USER.grps$' => $INFO['userinfo']['grps']
        ];

        $pattern = '/(' . implode('|', $operators) . ')/';
        $split = preg_split($pattern, $condition, 3, PREG_SPLIT_DELIM_CAPTURE);

        if (count($split) != 3) return false; //no valid condition

        list($left, $op, $right) = array_map('trim', $split);
        switch ($left) {
            case '$USER$':
                return $variables[$left] == $right;
            case '$USER.grps$':
                return is_array($variables[$left]) && in_array($right, $variables[$left]);
            default:
                return $left == $right;
        }
    }

    /**
     * Dummy implementation of an abstract method
     */
    public function html()
    {
        return '';
    }
}
