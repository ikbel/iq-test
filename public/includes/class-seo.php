<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class TYIQ_TestYourIqSeo
{
    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    const VERSION = '1.0.0';

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
        self::remove_canonical();
        self::remove_open_graph();

        //Create canonical
        add_action('wp_head', [$this, 'create_canonical']);

        //Create post title
        add_filter('document_title_parts', [$this, 'override_post_title'], 10);
        add_filter('wpseo_title', [$this, 'yoast_override_post_title']);
        add_filter('aioseop_title', [$this, 'all_IOS_override_post_title'], 1);
        add_action('wp_head', [$this, 'add_og_meta']);

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
     *
     */
    public function remove_canonical()
    {

        //Remove original canonical
        if (function_exists('rel_canonical')) {
            remove_action('wp_head', 'rel_canonical');
        }
        //Remove Yoast seo Canonical
        add_filter('wpseo_canonical', '__return_false');
        //Remove All in SEO canonical
        add_filter('aioseop_canonical_url', '__return_false');

        //Remove Japtack Open graph
        add_filter('jetpack_enable_open_graph', '__return_false');

    }

    /**
     *
     */
    public function remove_open_graph()
    {
        //Remove Japtack Open graph
        add_filter('jetpack_enable_open_graph', '__return_false');

    }

    /**
     *
     */
    public function create_canonical()
    {
        $link = get_permalink();
        echo "\n<link rel='canonical' href='" . esc_url($link) . "?iqresults=" . $_GET['iqresults'] . "' />\n";
    }

    /**
     * @param $title
     * @return mixed
     */
    public function override_post_title($title)
    {

        $title['title'] = esc_attr__('IQ Test Result', 'test-your-iq');
        $title['site'] = get_bloginfo("name"); //optional

        return $title;
    }

    /**
     * @param $title
     * @return mixed
     */
    public function yoast_override_post_title($title)
    {
        $title = esc_attr__('IQ Test Result', 'test-your-iq') . ' | ' . get_bloginfo("name");
        return $title;
    }

    /**
     * @param $title
     * @return mixed
     */
    public function all_IOS_override_post_title($title)
    {
        $title = esc_attr__('IQ Test Result', 'test-your-iq') . ' | ' . get_bloginfo("name");
        return $title;
    }

    public function add_og_meta()
    {

        $link = get_permalink();
        $link = esc_url($link) . "?iqresults=" . $_GET['iqresults'];
        $image_url = TYIQ_URL.'public/assets/images/iqtest_og_image.jpg';

        echo '' . "\n";
        echo '<!--GP IQ Test -->' . "\n";
        echo '<meta property="og:title" content="' . esc_attr__('IQ Test Result', 'test-your-iq') . '" />' . "\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:image" content="' . $image_url . '" />' . "\n";
        echo '<meta property="og:image:width" content="590" />' . "\n";
        echo '<meta property="og:image:height" content="300" />' . "\n";
        echo '<meta property="og:url" content="' . $link . '" />' . "\n";

        echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />' . "\n";
        echo '<!-GP IQ Test -->' . "\n";


    }


}
