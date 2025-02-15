<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

wp_enqueue_script('moment', TYIQ_URL . 'public/assets/js/moment.js', [], '1.0.0', false);
/**
 * If is set old session - delete
 */
if (isset($_SESSION['test_id'])) {
    unset($_SESSION['test_id']);
}
/**
 * New test_id and session
 */

$test_id = uniqid();
$_SESSION['test_id'] = $test_id;

$first_text = get_option('iq-first_text', '');
$second_text = get_option('iq-second_text', '');
$third_text = get_option('iq-third_text', '');

//Get Page ID
$page_id = $post->ID;

global $post;
$url = add_query_arg('question', 1, get_permalink($post->ID));

$button_type = get_option('iq-button_type');
if ($button_type == 'withboxes') {

    $html .= '<div class="iq-test-wrap iq-test-wrap-new">';

    $html .= '<div class="iq-test-pad">';
    $html .= '<div class="iq-test-header">';
    $html .= '<div class="iq-test-name2">' . esc_attr__('IQ Test - Online quiz', 'test-your-iq') . '</div>';
    $html .= '</div></div>';

    $html .= '<div class="iq-front-content">';
    $html .= '<div class="iq-first">' . stripslashes($first_text) . '</div>';

    $html .= '<div class="iq-startbutton-div iq-test-button"  data-loading="' . TYIQ_URL . '/public/assets/images/loading.svg"><div class="iq-startbutton iq-outline-inward" style="background-color: ' . get_option('iq-button_color', '#FF9900') . '"><span data-page="' . $page_id . '" data-test="' . $test_id . '" id="create_new_test" href="' . $url . '">' . esc_attr__('Take The Test', 'test-your-iq') . '</a></div></div>';

    $html .= '<div class="iq-thirds iq-thirds-one"><div class="iq-textinside">' . stripslashes($second_text) . '</div></div>';
    $html .= '<div class="iq-thirds iq-thirds-two"><div class="iq-textinside">' . stripslashes($third_text) . '</div></div>';

    $html .= '<div class="iq-clear"></div></div>';
    $html .= '</div>';

} else {
    $html .= '<div class="iq-test-wrap-new">';
    $html .= '<div class="iq-startbutton-div iq-test-button"  data-loading="' . TYIQ_URL . '/public/assets/images/loading.svg"><div class="iq-startbutton iq-outline-inward" style="background-color: ' . get_option('iq-button_color', '#FF9900') . '"><span data-page="' . $page_id . '" data-test="' . $test_id . '" id="create_new_test" href="' . $url . '">' . esc_attr__('Take The Test', 'test-your-iq') . '</a></div></div>';
    $html .= '</div>';
}
