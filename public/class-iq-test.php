<?php


class TYIQ_TestYourIq
{

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    const VERSION = '1.2';

    /**
     * Unique identifier for your plugin.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_slug = 'test-your-iq';

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct()
    {

        // Load plugin text domain
        add_action('init', [$this, 'load_plugin_textdomain']);

        // Activate plugin when new blog is added
        add_action('wpmu_new_blog', [$this, 'activate_new_site']);

        // Load public-facing style sheet and JavaScript.
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        add_action('init', [$this, 'init_shortcodes']);

        add_action('wp_ajax_new_iq_test', [$this, 'new_iq_test']);
        add_action('wp_ajax_nopriv_new_iq_test', [$this, 'new_iq_test']);

        add_action('wp_ajax_add_test_answer', [$this, 'add_test_answer']);
        add_action('wp_ajax_nopriv_add_test_answer', [$this, 'add_test_answer']);

        add_action('wp_ajax_set_age', [$this, 'set_age']);
        add_action('wp_ajax_nopriv_set_age', [$this, 'set_age']);

        add_action('wp_ajax_iq_after_payment', [$this, 'after_payment']);
        add_action('wp_ajax_iq_after_payment', [$this, 'after_payment']);

        add_action('wp_head', [$this, 'iq_test_ajaxurl']);
        add_action('init', [$this, 'register_session']);
        add_action('init', [$this, 'output_buffer']);


    }

    /**
     * Return the plugin slug.
     *
     * @return    Plugin slug variable.
     * @since    1.0.0
     *
     */
    public function get_plugin_slug()
    {
        return $this->plugin_slug;
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
     * Fired when the plugin is activated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     * @since    1.0.0
     *
     */
    public static function activate($network_wide)
    {


        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_activate();
                }

                restore_current_blog();

            } else {
                self::single_activate();
            }

        } else {
            self::single_activate();
        }


    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     * @since    1.0.0
     *
     */
    public static function deactivate($network_wide)
    {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_deactivate();

                }

                restore_current_blog();

            } else {
                self::single_deactivate();
            }

        } else {
            self::single_deactivate();
        }

    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @param int $blog_id ID of the new blog.
     * @since    1.0.0
     *
     */
    public function activate_new_site($blog_id)
    {

        if (1 !== did_action('wpmu_new_blog')) {
            return;
        }

        switch_to_blog($blog_id);
        self::single_activate();
        restore_current_blog();

    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @return   array|false    The blog ids, false if no matches.
     * @since    1.0.0
     *
     */
    private static function get_blog_ids()
    {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

        return $wpdb->get_col($sql);

    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    1.0.0
     */
    private static function single_activate()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        include('includes/install-tables.php');

    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    1.0.0
     */
    private static function single_deactivate()
    {

    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {

        $domain = $this->plugin_slug;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);


        load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, FALSE, basename(TYIQ_DIR) . '/languages/');

    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        global $post;
        if ($post) {
            $check_post = TYIQ_TestYourIq::check_post($post);
            if ($check_post) {
                wp_enqueue_style($this->plugin_slug . '-plugin-styles', plugins_url('assets/css/public.css', __FILE__), [], self::VERSION);
            }
        }

    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        global $post;
        if ($post) {
            $check_post = TYIQ_TestYourIq::check_post($post);
            if ($check_post) {
                wp_enqueue_script($this->plugin_slug . '-countdown', plugins_url('assets/js/countdown.js', __FILE__), ['jquery'], self::VERSION);
                wp_enqueue_script($this->plugin_slug . '-moment', plugins_url('assets/js/moment.js', __FILE__), ['jquery'], self::VERSION);
                wp_enqueue_script($this->plugin_slug . '-plugin-script', plugins_url('assets/js/public.js', __FILE__), ['jquery'], self::VERSION);

                //Get basic variables
                $iq_allow_paypal = get_option('iq-allow-paypal');
                if ($iq_allow_paypal == 'yes') {
                    $iq_client_id = get_option('iq-client-id', '');
                    $iq_currency = get_option('iq-currency', 'USD');
                    wp_enqueue_script($this->plugin_slug . '-paypal-script', 'https://www.paypal.com/sdk/js?client-id=' . $iq_client_id . '&currency=' . $iq_currency . '', ['jquery'], NULL);
                }

            }
        }

    }

    /**
     * Init shortcodes
     *
     * @since    1.0.0
     */
    public function init_shortcodes()
    {
        include('includes/shortcode-iq-test.php');
    }


    /**
     * Create new test
     *
     * @since 1.0.0
     */
    public function new_iq_test()
    {
        global $wpdb;
        $server_start = date('Y-m-d H:i:s');

        $wpdb->insert($wpdb->prefix . 'gp_iq_test',
            [
                'test_id' => $_POST['testid'],
                'start' => $_POST['starttime'],
                'server_start' => $server_start
            ]
        );
        if (!session_id()) {
            session_start();
            $_SESSION['test_id'] = $_POST['testid'];
        }

        echo do_shortcode('[test-your-iq]');
        exit();


    }

    /**
     * Save answer into database
     *
     * @since 1.0.0
     */
    public function add_test_answer()
    {
        global $wpdb;
        $result = $wpdb->insert($wpdb->prefix . 'gp_iq_test_data',
            [
                'test_id' => $_POST['testid'],
                'question' => $_POST['current'],
                'answer' => $_POST['id']
            ]
        );

        if ($_POST['current'] == 30) {
            $_POST['test'] = 'age';
        }

        echo do_shortcode('[test-your-iq]');
        exit();

    }

    /**
     * Save test age and test result
     *
     * @since 1.0.0
     */
    public function set_age()
    {

        global $wpdb;
        //Get start test
        $start = $wpdb->get_results(
            "SELECT start FROM " . $wpdb->prefix . "gp_iq_test WHERE test_id='" . $_POST['testid'] . "'"
        );

        //Contert start time to timestamp
        $start_time = $start[0]->start;
        $start_time = strtotime($start_time);

        //Get current time and calculate with timediff
        $end_time = $_POST['endtime'];
        $end_time = strtotime($end_time);

        //Get difference between start and end in minutes
        $time = $end_time - $start_time;
        $time = date('i:s', $time);
        $t = explode(':', $time);
        $s1 = (int)$t[0];
        $s2 = (int)$t[1];

        $s1 = 30 - $s1;
        if ($s2 > 0) {
            $s1 = $s1 - 1;
        }


        $seconds = ($s1 * 60) + $s2;
        if ($seconds > 15) {
            $second_points = round($seconds / 15);
        } else {
            $second_points = 0;
        }
        //Get current time for database update
        $end_time = $_POST['endtime'];

        /**
         *
         * Caluculate result
         *
         */
        //Get test data
        $correct_answers = TYIQ_TestYourIq::correct_answers();

        //Get all answers
        $test = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "gp_iq_test_data WHERE test_id='" . $_POST['testid'] . "'"
        );
        $points = 0;
        foreach ($test as $item) {
            if ($item->answer == $correct_answers[$item->question]) {
                $points = $points + 2;
            }
        }
        $iq = TYIQ_BASE_IQ + $points;

        //Age difference
        switch ($_POST['age']) {
            case '6-14':
            case '60-90':
                $iq = $iq + 10;
                break;
        }

        $count = count($correct_answers);
        $max_points = $count * 2 - 2; // if user get max points or have only one question wrong get boost for points from spare time.
        if ($points >= $max_points) {
            //Calculate seconds
            $iq = $iq + $second_points;
        }

        //special bonus for all answered questions
        $all_test_correct = $count * 2;
        if ($points >= $all_test_correct) {
            //Calculate seconds
            $iq = $iq + 20;
        }

        /**
         *
         * Update test data
         *
         */
        $data = [
            'age' => $_POST['age'],
            'end' => $_POST['endtime'],
            'time' => $time,
            'result' => $iq,
            'finish' => 'yes'
        ];

        $result = $wpdb->update(
            $wpdb->prefix . 'gp_iq_test',
            $data,
            ['test_id' => $_POST['testid']]);

        if (isset($_SESSION['test_id'])) {
            unset($_SESSION['test_id']);
        }

        echo do_shortcode('[test-your-iq]');

        exit();
    }

    public function after_payment()
    {
        global $wpdb;
        $today = current_time('mysql');
        $data = [
            'payment_date' => $today,
            'payment' => 'yes'
        ];

        $result = $wpdb->update(
            $wpdb->prefix . 'gp_iq_test',
            $data,
            ['test_id' => $_POST['testid']]);

        exit();

    }

    /**
     * Define ajax_url
     *
     * @since 1.0.0
     */
    public function iq_test_ajaxurl()
    {
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    }

    /**
     * Session start
     *
     * @simce 1.0.0
     */
    public function register_session()
    {
        if (!session_id())
            session_start();
    }

    /**
     *
     */
    public function output_buffer()
    {
        ob_start();
    }

    /**
     * @param $post
     * @param $type
     * @return bool
     */
    public static function check_post($post)
    {

        if (has_shortcode($post->post_content, 'test-your-iq')) {
            return true;
        }

        return false;

    }

    /**
     * @return array
     */
    public static function correct_answers()
    {

        $correct_answers = [
            '1' => '6',
            '2' => '2',
            '3' => '4',
            '4' => '2',
            '5' => '4',
            '6' => '1',
            '7' => '4',
            '8' => '2',
            '9' => '4',
            '10' => '1',
            '11' => '4',
            '12' => '4',
            '13' => '3',
            '14' => '3',
            '15' => '3',
            '16' => '3',
            '17' => '1',
            '18' => '3',
            '19' => '5',
            '20' => '5',
            '21' => '5',
            '22' => '6',
            '23' => '4',
            '24' => '2',
            '25' => '5',
            '26' => '2',
            '27' => '6',
            '28' => '2',
            '29' => '5',
            '30' => '1'

        ];

        return $correct_answers;

    }

    /**
     * @return array
     */
    public static function percentile()
    {

        $percents = [
            '68' => '1.64',
            '70' => '2.27',
            '72' => '3.09',
            '74' => '4.15',
            '76' => '5.47',
            '78' => '7.12',
            '80' => '9.12',
            '82' => '11.50',
            '84' => '14.30',
            '86' => '17.53',
            '88' => '21.18',
            '90' => '25.24',
            '92' => '29.69',
            '94' => '34.45',
            '96' => '39.48',
            '98' => '44.69',
            '100' => '50.00',
            '102' => '55.30',
            '104' => '60.51',
            '106' => '65.54',
            '108' => '70.30',
            '110' => '74.75',
            '112' => '78.81',
            '114' => '82.46',
            '116' => '85.69',
            '118' => '88.49',
            '120' => '90.87',
            '122' => '92.87',
            '124' => '94.52',
            '126' => '95.84',
            '128' => '96.90',
            '129' => '97.34',
            '130' => '97.72',
            '131' => '98.06',
            '132' => '98.35',
            '133' => '98.60',
            '134' => '98.82',
            '135' => '99.01',
            '136' => '99.18',
            '137' => '99.31',
            '138' => '99.43',
            '139' => '99.53'
        ];

        return $percents;

    }

}

?>