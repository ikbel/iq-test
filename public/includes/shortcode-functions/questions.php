<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

$correct_answers = TYIQ_TestYourIq::correct_answers();

$total = count($correct_answers);
global $wpdb;

if (isset($_GET['question'])) {
    $question = sanitize_text_field($_GET['question']);
} elseif (isset($_POST['question'])) {
    $question = sanitize_text_field($_POST['question']);
}


$next = (empty($question) ? 2 : $question + 1);
$current = (empty($question) ? 1 : $question);

$test_time = 1800;

$button_color = get_option('iq-button_color', '#FF9900');

if (isset($_SESSION['test_id'])) {
    $test_id = $_SESSION['test_id'];
} else {
    $_POST[] = '';
    $html = do_shortcode('[test-your-iq]');
}

if (isset($_POST['page_id'])) {
    $page_id = sanitize_text_field($_POST['page_id']);
} else {
    $page_id = get_the_ID();
}


if ($total == $current) {
    $url = add_query_arg('test', 'age', get_permalink($page_id));
} else {
    $url = add_query_arg('question', $next, get_permalink($page_id));
}

$start = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE test_id='" . $test_id . "'");


$countdown = date('Y-m-d H:i:s', strtotime($start->start) + $test_time);
$e_count = strtotime($start->server_start) + $test_time;
$e_count = date("Y-m-d H:i:s", $e_count);
$now = date('Y-m-d H:i:s');

if ($now > $e_count) {

    $_POST['test'] = 'age';
    $_POST['time_expired'] = true;
    $html = do_shortcode('[test-your-iq]');

} else {

    $procent = $current * 3.33;

    $from = $current - 1;
    $fromprocent = $from * 3.33;

    if (is_admin_bar_showing()) {
        $html .= '<a name=iqtop class="iq-top"></a><div class="iq-bar-show"></div>';
    } else {
        $html .= '<a name=iqtop class="iq-top"></a>';
    }

    $html .= '<div class="iq-test-wrap">';

    //Button color
    $encoded_stl = base64_decode('PHN0eWxlPgogICAgICAgICAgICAgICAgLmlxLWFuc3dlcnMgaW1nOmhvdmVyIHsKICAgICAgICAgICAgICAgICAgICAtd2Via2l0LWJveC1zaGFkb3c6IDBweCAwcHggMHB4IDNweCAnIC4geHh4IC4gJyAhaW1wb3J0YW50OwogICAgICAgICAgICAgICAgICAgIGJveC1zaGFkb3c6IDBweCAwcHggMHB4IDNweCAnIC4geHh4IC4gJyAhaW1wb3J0YW50OwogICAgICAgICAgICAgICAgfQogICAgICAgICAgICAgIDwvc3R5bGU+');
    $orig = ['xxx'];
    $new = [$button_color];
    $new_stl = str_replace($orig, $new, $encoded_stl);
    $html .= $new_stl;

    //Countdown
    $encoded_src = base64_decode('PHNjcmlwdD4KICAgICAgICAgICAgKGZ1bmN0aW9uICggJCApIHsKCSJ1c2Ugc3RyaWN0IjsKCgkkKGZ1bmN0aW9uICgpIHsgICAgICAKICAgICAgICAgICAgICAgICBqUXVlcnkoJyNjb3VudGRvd24nKS5jb3VudGRvd24oInp6eiIsIGZ1bmN0aW9uKGV2ZW50KXsKICAgICAgICAgICAgdmFyIHRvdGFsSG91cnMgPSBldmVudC5vZmZzZXQudG90YWxEYXlzICogMjQgKyBldmVudC5vZmZzZXQuaG91cnM7CiAgICAgICAgICAgIGpRdWVyeSh0aGlzKS5odG1sKGV2ZW50LnN0cmZ0aW1lKCAnICVNIHh4eCAlUyB5eXknKSk7CiAgICAgICAgICAgICAgICAgIH0pOwogICAgICAgICAgICAgICB9KTsgICAKICAgICAgICAgICAgIH0oalF1ZXJ5KSk7ICAKICAgICAgICAgICAgPC9zY3JpcHQ+');
    $min_value = esc_attr__('min', 'test-your-iq');
    $sec_value = esc_attr__('sec', 'test-your-iq');
    $orig = ['xxx', 'yyy', 'zzz'];
    $new = [$min_value, $sec_value, $countdown];
    $new_scr = str_replace($orig, $new, $encoded_src);
    $html .= $new_scr;

    
    $html .= '<div class="iq-test-pad">';

    $html .= '<div class="iq-test-header">';
    $html .= '<div class="iq-test-name">' . sprintf(esc_attr__('Question %s/30', 'test-your-iq'), $current) . '</div>';
    $html .= '<div id="countdown"></div>';
    $html .= '<div class="iq-clear"></div></div>';

    //Progress bar progress
    $encoded_stl = base64_decode('PHN0eWxlIHR5cGU9InRleHQvY3NzIj5ALXdlYmtpdC1rZXlmcmFtZXMgcHJvZ3Jlc3MgeyAKICAgICAgICAgICAgICAgIGZyb20ge3h4eCV9CiAgICAgICAgICAgICAgICB0byB7eXl5JX0KICAgICAgICAgICAgfQogICAgICAgICAgICAKICAgICAgICAgICAgQC1tb3ota2V5ZnJhbWVzIHByb2dyZXNzIHsgCiAgICAgICAgICAgICAgICBmcm9tIHsgd2lkdGg6IHh4eCV9CiAgICAgICAgICAgICAgICB0byB7IHdpZHRoOiB5eXklfQogICAgICAgICAgICB9CiAgICAgICAgICAgIAogICAgICAgICAgICBALW1zLWtleWZyYW1lcyBwcm9ncmVzcyB7IAogICAgICAgICAgICAgICAgZnJvbSB7d2lkdGg6IHh4eCUgfQogICAgICAgICAgICAgICAgdG8geyB3aWR0aDogeXl5JSB9CiAgICAgICAgICAgIH0KICAgICAgICAgICAgCiAgICAgICAgICAgIEBrZXlmcmFtZXMgcHJvZ3Jlc3MgeyAKICAgICAgICAgICAgICAgIGZyb20ge3dpZHRoOiB4eHglIH0KICAgICAgICAgICAgICAgIHRvIHsgd2lkdGg6IHl5eSUgfQogICAgICAgICAgICB9CiAgICAgICAgICAgIDwvc3R5bGU+');
    $orig = ['xxx', 'yyy'];
    $new = [$fromprocent, $procent];
    $new_stl = str_replace($orig, $new, $encoded_stl);
    $html .= $new_stl;

    $html .= '<div class="iq-clear"></div>';
    $html .= '<div id="iq-progressbar"><div id="progress"><div id="pbaranim" style="background-color: ' . $button_color . '"></div></div></div>';
    $html .= '<div class="iq-clear"></div>';
    $html .= '<div id="iq-question-wrap">';

    $html .= '<div class="iq-questions">';

    $html .= '<img src="' . TYIQ_URL . 'public/assets/images/questions/' . $current . '.jpg" alt="" />';
    $html .= '</div>';

    $html .= '<div class="iq-answers">';
    $html .= '<div class="iq-answers-pad">';


    for ($c = 1; $c < 7; $c++) {
        if ($c % 2) {
            $html .= '<span class="gp-answer-link iq-answer-link answer-link-image" href="' . $url . '" data-id="' . $c . '" data-test="' . $test_id . '" data-current="' . $current . '" data-page="' . $page_id . '">';
            $html .= '<img src="' . TYIQ_URL . 'public/assets/images/questions/answers/' . $current . '-' . $c . '.jpg" alt="" class="iq-border-fade" />';
            $html .= '</span>';
        } else {
            $html .= '<span class="gp-answer-link iq-answer-link answer-link-image" href="' . $url . '" data-id="' . $c . '" data-test="' . $test_id . '" data-current="' . $current . '" data-page="' . $page_id . '">';
            $html .= '<img src="' . TYIQ_URL . 'public/assets/images/questions/answers/' . $current . '-' . $c . '.jpg" alt="" class="iq-border-fade sud" />';
            $html .= '</span>';
        }
    }
    $html .= '<div class="iq-clear"></div></div></div>';

    $html .= '</div>';
    $html .= '';
    $html .= '';
    $html .= '';
    $html .= '<div class="iq-clear"></div></div>';
    $html .= '<div class="iq-leave_question-div iq-test-button" data-loading="' . TYIQ_URL . '/public/assets/images/loading.svg"><div class="gp-answer-link iq-leave_question iq-outline-inward" style="background-color: ' . $button_color . '" data-id="99" data-test="' . $test_id . '" data-current="' . $current . '" data-page="' . $page_id . '" href="' . $url . '"><span class="iq-jump-question">' . __('Skip this question!', 'test-your-iq') . '</span></div></div>';
    $html .= '<div class="iq-clear"></div></div>';
}


