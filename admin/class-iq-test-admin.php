<?php

class TYIQ_TestYourIqAdmin
{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct()
    {

        $plugin = TYIQ_TestYourIq::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // Add the options page and menu item.
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);

        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_slug . '.php');
        add_filter('plugin_action_links_' . $plugin_basename, [$this, 'add_action_links']);

        add_action('admin_init', [$this, 'output_buffer']);

    }

    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     * @since     1.0.0
     *
     */
    public static function get_instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @return    null    Return early if no settings page is registered.
     * @since     1.0.0
     *
     */
    public function enqueue_admin_styles()
    {

        wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), [], TYIQ_TestYourIq::VERSION);

    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @return    null    Return early if no settings page is registered.
     * @since     1.0.0
     *
     */
    public function enqueue_admin_scripts()
    {

        wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), TYIQ_TestYourIq::VERSION );

    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu()
    {

        add_menu_page(
            esc_attr__('General Settings', $this->plugin_slug),
            esc_attr__('IQ Test', $this->plugin_slug),
            'manage_options',
            $this->plugin_slug,
            [$this, 'display_plugin_setting_page']
        );
        add_submenu_page($this->plugin_slug, esc_attr__('General Settings', $this->plugin_slug), esc_attr__('General Settings', $this->plugin_slug), 'manage_options', $this->plugin_slug);
        add_submenu_page(
            $this->plugin_slug,
            esc_attr__('Tests', $this->plugin_slug),
            esc_attr__('Tests', $this->plugin_slug),
            'manage_options',
            $this->plugin_slug . '-setting',
            [$this, 'display_plugin_admin_page']
        );
        add_submenu_page(
            $this->plugin_slug,
            esc_attr__('PayPal', $this->plugin_slug),
            esc_attr__('PayPal', $this->plugin_slug),
            'manage_options',
            $this->plugin_slug . '-paypal',
            [$this, 'display_plugin_paypal']
        );
        add_submenu_page(
            $this->plugin_slug,
            esc_attr__('Info & Help', $this->plugin_slug),
            esc_attr__('Info & Help', $this->plugin_slug),
            'manage_options',
            $this->plugin_slug . '-help',
            [$this, 'display_plugin_help_page']
        );

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page()
    {
        include_once('views/tests.php');
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_help_page()
    {
        include_once('views/help.php');
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setting_page()
    {
        include_once('views/setting.php');
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_paypal()
    {
        include_once('views/paypal.php');
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links)
    {

        return array_merge(
            [
                'settings' => '<a href="' . admin_url('options-general.php?page=' . $this->plugin_slug) . '">' . esc_attr__('Settings', $this->plugin_slug) . '</a>'
            ],
            $links
        );

    }


    public function output_buffer()
    {
        ob_start();
    }


    /**
     * @return array
     */
    public static function paypal_currency()
    {
        $currencies = [
            'AUD' => [
                'label' => 'Australian Dollar',
                'format' => '$ %s',
            ],
            'CAD' => [
                'label' => 'Canadian Dollar',
                'format' => '$ %s',
            ],
            'EUR' => [
                'label' => 'Euro',
                'format' => '€ %s',
            ],
            'GBP' => [
                'label' => 'Pound Sterling',
                'format' => '£ %s',
            ],
            'JPY' => [
                'label' => 'Japanese Yen',
                'format' => '¥ %s',
            ],
            'USD' => [
                'label' => 'U.S. Dollar',
                'format' => '$ %s',
            ],
            'NZD' => [
                'label' => 'N.Z. Dollar',
                'format' => '$ %s',
            ],
            'CHF' => [
                'label' => 'Swiss Franc',
                'format' => '%s Fr',
            ],
            'HKD' => [
                'label' => 'Hong Kong Dollar',
                'format' => '$ %s',
            ],
            'SGD' => [
                'label' => 'Singapore Dollar',
                'format' => '$ %s',
            ],
            'SEK' => [
                'label' => 'Swedish Krona',
                'format' => '%s kr',
            ],
            'DKK' => [
                'label' => 'Danish Krone',
                'format' => '%s kr',
            ],
            'PLN' => [
                'label' => 'Polish Zloty',
                'format' => '%s zł',
            ],
            'NOK' => [
                'label' => 'Norwegian Krone',
                'format' => '%s kr',
            ],
            'HUF' => [
                'label' => 'Hungarian Forint',
                'format' => '%s Ft',
            ],
            'CZK' => [
                'label' => 'Czech Koruna',
                'format' => '%s Kč',
            ],
            'ILS' => [
                'label' => 'Israeli New Sheqel',
                'format' => '₪ %s',
            ],
            'MXN' => [
                'label' => 'Mexican Peso',
                'format' => '$ %s',
            ],
            'BRL' => [
                'label' => 'Brazilian Real',
                'format' => 'R$ %s',
            ],
            'MYR' => [
                'label' => 'Malaysian Ringgit',
                'format' => 'RM %s',
            ],
            'PHP' => [
                'label' => 'Philippine Peso',
                'format' => '₱ %s',
            ],
            'TWD' => [
                'label' => 'New Taiwan Dollar',
                'format' => 'NT$ %s',
            ],
            'THB' => [
                'label' => 'Thai Baht',
                'format' => '฿ %s',
            ],
            'TRY' => [
                'label' => 'Turkish Lira',
                'format' => 'TRY %s', // Unicode is ₺ but this doesn't seem to be widely supported yet (introduced Sep 2012)
            ],
        ];
        return $currencies;

    }

    /**
     * @return array
     */
    public static function get_basic_texts(){

        $text = [];

        $text['top'] = "An IQ test is a psychological assessment that measures a range of cognitive abilities and provides a score that is intended to serve as a measure of an individual's intellectual abilities and potential.";
        $text['left'] = "<h2>About the test</h2>
                            <ul>
                                <li>The test takes 30 minutes</li>
                                <li>You have to answer 30 puzzles</li>
                                <li>The puzzles are randomly arranged from easy to difficult</li>
                                <li>After completing the test you have to pay a small fee of 0.99 USD</li>
                                <li>You will be shown the test result</li>
                                <li>You will also be shown all wrong answers</li>
                            </ul>";
        $text['right'] = "<h2>Intelligence quotient</h2>
                            An intelligence quotient (IQ) is a total score derived from a set of standardized tests or subtests designed to assess human intelligence. The abbreviation \"IQ\" was coined by the psychologist William Stern for the German term Intelligenzquotient, his term for a scoring method for intelligence tests at the University of Breslau he advocated in a 1912 book.";


        return $text;

    }

}

?>