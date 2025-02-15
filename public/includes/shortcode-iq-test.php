<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Control if function exist
 */
if (!function_exists('iq_test_shortcode')) {

    /**
     * Shortcode function
     */
    function tyiq_shortcode()
    {
        global $post;

        //Allow to load necessary files only when needed
        if (!empty($post) && TYIQ_TestYourIq::check_post($post) || (defined('DOING_AJAX') || DOING_AJAX)) {

        $html = '<div id="iq-info-data-bar" data-devmod="'.TYIQ_DEV_MOD.'" style="display: none;" ></div>';

                if (isset($_GET['test']) || isset($_POST['test'])) {
                    include('shortcode-functions/age.php');
                } elseif (isset($_GET['iqresults']) || isset($_POST['iqresults'])) {
                    include('shortcode-functions/results.php');
                } elseif (isset($_GET['question']) || isset($_POST['question'])) {
                    include('shortcode-functions/questions.php');
                } else {
                    include('shortcode-functions/start.php');
                }

                return $html;
            }
    }

    /**
     * Add shortcode
     */
    add_shortcode('test-your-iq', 'tyiq_shortcode');

}
