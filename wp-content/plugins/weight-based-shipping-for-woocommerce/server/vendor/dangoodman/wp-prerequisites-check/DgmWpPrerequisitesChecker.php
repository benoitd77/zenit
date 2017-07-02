<?php
class WbsVendors_DgmWpPrerequisitesChecker
{
    static public function createAndCheck($pluginName, $phpVersion, $wpVersion, $wcVersion = null)
    {
        $instance = new self($pluginName, $phpVersion, $wpVersion, $wcVersion);
        return $instance->check();
    }

    public function __construct($pluginName, $phpVersion, $wpVersion, $wcVersion = null)
    {
        $this->pluginName = $pluginName;
        $this->phpVersion = $phpVersion;
        $this->wpVersion = $wpVersion;
        $this->wcVersion = $wcVersion;

        // Hook admin_notices always since errors can be added lately
        add_action('admin_notices', array($this, '_showErrors'));
    }

    public function check()
    {
        $this->errors = array();

        if (version_compare($phpv = PHP_VERSION, $this->phpVersion, '<')) {
            $this->errors[] =
                "You are running an outdated PHP version {$phpv}. 
                 {pluginName} requires PHP {phpVersion}+. 
                 Contact your hosting support to switch to a newer PHP version.";
        }
        
        global $wp_version;
        if (isset($wp_version) && version_compare($wp_version, $this->wpVersion, '<')) {
            $this->errors[] =
                "You are running an outdated WordPress version {$wp_version}.
                 {pluginName} is tested with WordPress {wpVersion}+.
                 Consider updating to a modern WordPress version.";
        }

        if (isset($this->wcVersion)) {
            if (!self::isWoocommerceActive()) {
                $this->errors[] =
                    "WooCommerce is not active. 
                     {pluginName} requires WooCommerce to be installed and activated.";
            } else {
                if (defined('WC_VERSION') || did_action('plugins_loaded')) {
                    $this->_checkWoocommerceVersion();
                } else {
                    add_action('plugins_loaded', array($this, '_checkWoocommerceVersion'));
                }
            }
        }

        return !$this->errors;
    }

    public function _showErrors()
    {
        if (!$this->errors) {
            return;
        }

        ?>
            <div class="notice notice-error">
                <?php foreach ($this->errors as $error): ?>
                    <?php
                        $error = strtr($error, array(
                            '{pluginName}' => $this->pluginName,
                            '{phpVersion}' => $this->phpVersion,
                            '{wpVersion}' => $this->wpVersion,
                            '{wcVersion}' => $this->wcVersion,
                        ));
                    ?>
                    <p><?php echo esc_html($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php
    }

    public function _checkWoocommerceVersion()
    {
        $wcVersion = defined('WC_VERSION') ? WC_VERSION : null;

        if (!isset($wcVersion) || version_compare($wcVersion, $this->wcVersion, '<')) {
            $this->errors[] =
                "You are running an outdated WooCommerce version".(isset($wcVersion) ? " ".$wcVersion : null).".
                 {pluginName} requires WooCommerce {wcVersion}+.
                 Consider updating to a modern WooCommerce version.";
        }
    }


    static private function isWoocommerceActive()
    {
        static $active_plugins;

        if (!isset($active_plugins)) {
            $active_plugins = (array)get_option('active_plugins', array());
            if (is_multisite()) {
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
            }
        }

        return
            in_array('woocommerce/woocommerce.php', $active_plugins) ||
            array_key_exists('woocommerce/woocommerce.php', $active_plugins);
    }


    private $pluginName;
    private $phpVersion;
    private $wpVersion;
    private $wcVersion;

    private $errors;
}