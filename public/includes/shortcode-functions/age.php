<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

wp_enqueue_script('moment', TYIQ_URL . 'public/assets/js/moment.js', [], '1.0.0', false);

if (!$_SESSION['test_id']) {
    wp_redirect(get_permalink($post->ID));
}

if (isset($_POST['page_id'])) {
    $page_id = sanitize_text_field($_POST['page_id']);
} else {
    $page_id = get_the_ID();
}

$button_color = get_option('iq-button_color', '#FF9900');

global $post;
$url = add_query_arg('results', $_SESSION['test_id'], get_permalink($page_id));

$html = '';
if (is_admin_bar_showing()) {
    $html .= '<a name=iqtop class="iq-top"></a><div class="iq-bar-show"></div>';
} else {
    $html .= '<a name=iqtop class="iq-top"></a>';
}

$html .= '<div class="iq-test-wrap iq_min_height">';
$html .= '<div class="iq-test-pad">';
$html .= '<div class="iq-test-header">';

//Check the expired time
if (isset($_POST['time_expired'])) {
    $html .= '<div class="iq-test-name">' . esc_attr__('Your test time expired!', 'test-your-iq') . '</div>';
} else {
    $html .= '<div class="iq-test-name">' . esc_attr__('Question 30/30', 'test-your-iq') . '</div>';
}


$html .= '<div class="iq-clear"></div></div>';

//Progress bar progress
$encoded_stl = base64_decode('PHN0eWxlIHR5cGU9InRleHQvY3NzIj5ALXdlYmtpdC1rZXlmcmFtZXMgcHJvZ3Jlc3MgeyAKICAgICAgICAgICAgICAgIGZyb20ge3h4eCV9CiAgICAgICAgICAgICAgICB0byB7eXl5JX0KICAgICAgICAgICAgfQogICAgICAgICAgICAKICAgICAgICAgICAgQC1tb3ota2V5ZnJhbWVzIHByb2dyZXNzIHsgCiAgICAgICAgICAgICAgICBmcm9tIHsgd2lkdGg6IHh4eCV9CiAgICAgICAgICAgICAgICB0byB7IHdpZHRoOiB5eXklfQogICAgICAgICAgICB9CiAgICAgICAgICAgIAogICAgICAgICAgICBALW1zLWtleWZyYW1lcyBwcm9ncmVzcyB7IAogICAgICAgICAgICAgICAgZnJvbSB7d2lkdGg6IHh4eCUgfQogICAgICAgICAgICAgICAgdG8geyB3aWR0aDogeXl5JSB9CiAgICAgICAgICAgIH0KICAgICAgICAgICAgCiAgICAgICAgICAgIEBrZXlmcmFtZXMgcHJvZ3Jlc3MgeyAKICAgICAgICAgICAgICAgIGZyb20ge3dpZHRoOiB4eHglIH0KICAgICAgICAgICAgICAgIHRvIHsgd2lkdGg6IHl5eSUgfQogICAgICAgICAgICB9CiAgICAgICAgICAgIDwvc3R5bGU+');
$orig = ['xxx', 'yyy'];
$new = ['100', '100'];
$new_stl = str_replace($orig, $new, $encoded_stl);
$html .= $new_stl;

$html .= '<div id="iq-progressbar"><div id="progress"><div id="pbaranim" style="background-color: ' . $button_color . '"></div></div></div>';
$html .= '<div class="iq-select">';
$html .= '<div class="iq-select-text"><small>' . esc_attr__('Final Step', 'test-your-iq') . '</small><br />' . esc_attr__('Select Your Age!', 'test-your-iq') . '</div>';
$html .= '<input type="hidden" name="user_age_location" id="user_age_location"  value="' . $url . '" >';
$html .= '<input type="hidden" name="test_id" id="test_id"  value="' . $_SESSION['test_id'] . '" >';
$html .= '</div>';

$html .= '<div class="iq-leave_question-div iq-test-button iq-age-button"  data-age="6-14" data-loading="' . TYIQ_URL . '/public/assets/images/loading.svg"><div class="iq-leave_question iq-outline-inward"><span style="background-color: ' . $button_color . '" class="iq-jump-question">' . esc_attr__('Age', 'test-your-iq') . ' 6-14</span></div></div>';
$html .= '<div class="iq-leave_question-div iq-test-button iq-age-button" data-age="14-30" data-loading="' . TYIQ_URL . '/public/assets/images/loading.svg"><div class="iq-leave_question iq-outline-inward"><span style="background-color: ' . $button_color . '"class="iq-jump-question">' . esc_attr__('Age', 'test-your-iq') . ' 14-30</span></div></div>';
$html .= '<div class="iq-leave_question-div iq-test-button iq-age-button" data-age="30-60" data-loading="' . TYIQ_URL . '/public/assets/images/loading.svg"><div class="iq-leave_question iq-outline-inward"><span style="background-color: ' . $button_color . '"class="iq-jump-question">' . esc_attr__('Age', 'test-your-iq') . ' 30-60</span></div></div>';
$html .= '<div class="iq-leave_question-div iq-test-button iq-age-button" data-age="60-90" data-loading="' . TYIQ_URL . '/public/assets/images/loading.svg"><div class="iq-leave_question iq-outline-inward"><span style="background-color: ' . $button_color . '"class="iq-jump-question">' . esc_attr__('Age', 'test-your-iq') . ' 60-90</span></div></div>';
$html .= '<div class="iq-clear"></div></div>';

$html .= '</div></div>';