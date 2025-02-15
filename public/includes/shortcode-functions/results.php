<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

$result_type = get_option('iq-result_type');
if (empty($result_type)) {
    $result_type = 'points';
}

global $wpdb;

if (isset($_GET['iqresults'])) {
    $results = sanitize_text_field($_GET['iqresults']);
} elseif (isset($_POST['iqresults'])) {
    $results = sanitize_text_field($_POST['iqresults']);
} else {
    $results = false;
}

//Get page URL
if (isset($_POST['permalink'])) {
    $permalink = sanitize_text_field($_POST['permalink']);
} else {
    $permalink = get_permalink();
}


//Get Test from DB
if ($results) {
    $test_id = htmlspecialchars($results);
    $test = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE test_id='" . $test_id . "'");
}


$html = '';
if (is_admin_bar_showing()) {
    $html .= '<a name=iqtop class="iq-top"></a><div class="iq-bar-show"></div>';
} else {
    $html .= '<a name=iqtop class="iq-top"></a>';
}
$html .= '<a name=iqtop class="iq-top"></a>';


//Paypal Check
$paypal_check = '';
$iq_allow_paypal = get_option('iq-allow-paypal');
if ($iq_allow_paypal == 'yes' && $results && $test[0]->payment == 'no') {
    include_once(TYIQ_DIR . 'public/includes/shortcode-functions/payment.php');
    $paypal_check = ' style="display:none"';
}

$html .= '<div class="iq-test-wrap" id="iq-test-result"' . $paypal_check . '>';
$html .= '<div class="iq-test-pad">';

$html .= '<div class="iq-test-header">';


if ($results) {

    $correct_answers = TYIQ_TestYourIq::correct_answers();
    $percents = TYIQ_TestYourIq::percentile();

    if (!empty($test)) {

        if ($test[0]->finish == 'no') {

            $html .= '<p>' . esc_attr__('Test with this ID was not finished.', 'test-your-iq') . '</p>';

        } else {

            $test_result = $test[0]->result;


            if ($test_result < 69) {
                $test_info = esc_attr__('IQ CLASSIFICATION - Lower extreme!', 'test-your-iq');
            } elseif ($test_result > 69 && $test_result < 80) {
                $test_info = esc_attr__('IQ CLASSIFICATION - Well below average!', 'test-your-iq');
            } elseif ($test_result > 79 && $test_result < 90) {
                $test_info = esc_attr__('IQ CLASSIFICATION - Low Average!', 'test-your-iq');
            } elseif ($test_result > 89 && $test_result < 110) {
                $test_info = esc_attr__('IQ CLASSIFICATION - Average!', 'test-your-iq');
            } elseif ($test_result > 109 && $test_result < 120) {
                $test_info = esc_attr__('IQ CLASSIFICATION - High Average!', 'test-your-iq');
            } elseif ($test_result > 119 && $test_result < 130) {
                $test_info = esc_attr__('IQ CLASSIFICATION - Well above average!', 'test-your-iq');
            } elseif ($test_result > 129) {
                $test_info = esc_attr__('IQ CLASSIFICATION - Upper extreme!', 'test-your-iq');
            }


            $unit = '';

            $nasobek = $test_result * 0.066;
            if ($result_type == 'pointsplus') {

                if ($test_result > 139) {
                    $test_result = round($test_result + $nasobek);
                } else {
                    $test_result = round($test_result + $nasobek);
                }
                $unit = '';
            }

            if ($result_type == 'percent') {
                if ($test_result > 200) {
                    $test_result = '99.95';
                } elseif ($test_result > 179) {
                    $test_result = '99.8';
                } elseif ($test_result > 159) {
                    $test_result = '99.7';
                } elseif ($test_result > 139) {
                    $test_result = '99.6';
                } else {
                    $test_result = $percents[$test[0]->result];
                }
                $unit = '%';
                $hundred = '100';
                $zbytek = round($hundred - $test_result, 3);
            }

            if ($result_type == 'pointsplus' or $result_type == 'points') {
                $html .= '<div class="iq-congratulation">' . esc_attr__('CONGRATULATIONS!', 'test-your-iq') . ' ' . esc_attr__('YOUR IQ IS', 'test-your-iq') . ' ' . $test_result . '' . $unit . '</div>';
                $html .= '<div class="iq-congratulation qresult">IQ ' . $test_result . '' . $unit . '</div>';
                $html .= '<div class="iq-clear"></div></div>';
                $html .= '<div class="iq-ev">' . $test_info . '</div>';
                $html .= '<div class="iq-testid">' . esc_attr__('ID of the Test:', 'test-your-iq') . ' ' . $test[0]->test_id . '</div>';
            }
            if ($result_type == 'percent') {
                $html .= '<div class="iq-congratulation">' . esc_attr__('CONGRATULATIONS!', 'test-your-iq') . ' ' . esc_attr__('YOUR IQ TEST RESULT IS', 'test-your-iq') . ' ' . $test_result . '' . $unit . '</div>';
                $html .= '<div class="iq-congratulation qresult">' . $test_result . '' . $unit . '</div>';
                $html .= '<div class="iq-clear"></div></div>';
                $html .= '<div class="iq-ev">' . $zbytek . '% ' . esc_attr__('of Population is smarter then You!', 'test-your-iq') . '</div>';
                $html .= '<div class="iq-ev2">' . $test_info . '</div>';
                $html .= '<div class="iq-testid">' . esc_attr__('ID of the Test:', 'test-your-iq') . ' ' . $test[0]->test_id . '</div>';
            }

            //Add social icons
            $allow_social = get_option('iq-allow_social', 'yes');
            if ($allow_social == 'yes') {
                $image_url = TYIQ_URL.'public/assets/images/iqtest_og_image.jpg';
                $social_icons = get_option('iq-allow_social-icons', ['facebook' => 1, 'twitter' => 1, 'pinterest' => 1, 'whatsapp' => 1, 'vkontakte' => 1]);
                $share_url_encode = urlencode($permalink.'?iqresults='. $test[0]->test_id);
                $title = esc_attr__('My IQ Test Result', 'test-your-iq');
                $html .='<div class="iq-social-icons-box">';
                if ($social_icons['facebook'] == '1') {
                    $html .= '<span class="iq-social-icons"><a href="https://www.facebook.com/sharer.php?u=' . $share_url_encode . '" target="_blank"><img src="' . TYIQ_URL . 'public/assets/images/social_icons/facebook.png"></a></span>';
                }
                if ($social_icons['twitter'] == '1') {
                    $html .= '<span class="iq-social-icons"><a href="https://twitter.com/intent/tweet?source=SOURCE&text=' . $title . '&url=' . $share_url_encode . '" target="_blank"><img src="' . TYIQ_URL . 'public/assets/images/social_icons/twitter.png"></a></span>';
                }
                if ($social_icons['pinterest'] == '1') {
                    $html .= '<span class="iq-social-icons"><a href="https://pinterest.com/pin/create/link/?url=' . $share_url_encode . '&description=' . $title . '&media=' . urlencode($image_url) . '" target="_blank"><img src="' . TYIQ_URL . 'public/assets/images/social_icons/pinterest.png"></a></span>';
                }
                if ($social_icons['whatsapp'] == '1') {
                    $html .= '<span class="iq-social-icons"><a href="whatsapp://send?text=' . $share_url_encode . '" data-action="share/whatsapp/share" target="_blank"><img src="' . TYIQ_URL . 'public/assets/images/social_icons/whatsapp.png"></a></span>';
                }
                if ($social_icons['vkontakte'] == '1') {
                    $html .= '<span class="iq-social-icons"><a href="https://vk.com/share.php?url=' . $share_url_encode . '" target="_blank"><img src="' . TYIQ_URL . 'public/assets/images/social_icons/vkontakte.png"></a></span>';
                }
                $html .='</div>';


            }

            $answers = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test_data WHERE test_id='" . $test_id . "'");

            $i = 0;
            foreach ($answers as $item) {
                if ($item->answer != $correct_answers[$item->question]) {
                    $i++;
                }
            }


            if ($i > 0) {
                $html .= '<div class="iq-correctanswers">';
                $html .= '<div class="iq-hereare">' . esc_attr__('Here are correct answers for questions, in which you made a mistake:', 'test-your-iq') . '</div>';

            } else {

                $html .= '<div class="iq-correctanswers">';
                $html .= '<div class="iq-hereare">' . esc_attr__('All your answers are correct!', 'test-your-iq') . '</div>';
            }

            //Wrong answers
            foreach ($answers as $item) {

                if ($item->answer != $correct_answers[$item->question]) {

                    $current = $item->question;
                    $html .= '<div id="iq-question-wrap">';
                    $html .= '<div class="iq-question_number">' . esc_attr__('Questions no.' . $current . ' ', 'test-your-iq') . '</div>';
                    $html .= '<div class="iq-questions">';
                    $html .= '<img src="' . TYIQ_URL . 'public/assets/images/questions/' . $current . '.jpg" alt="" />';
                    $html .= '</div>';

                    $html .= '<div class="iq-answers">';
                    $html .= '<div class="iq-answers-pad">';

                    for ($c = 1; $c < 7; $c++) {

                        if ($item->answer == $c || $item->answer == 99 && $c != $correct_answers[$item->question]) {
                            $class = 'iq-incorrect';
                        } elseif ($c == $correct_answers[$item->question]) {
                            $class = 'iq-correct';
                        } else {
                            $class = '';
                        }

                        if ($c % 2) {
                            $html .= '<img  src="' . TYIQ_URL . 'public/assets/images/questions/answers/' . $current . '-' . $c . '.jpg" alt="" class="' . $class . '" />';
                        } else {
                            $html .= '<img  src="' . TYIQ_URL . 'public/assets/images/questions/answers/' . $current . '-' . $c . '.jpg" alt="" class="sud ' . $class . '" />';
                        }

                    }
                    $html .= '<div class="iq-clear"></div></div></div>';
                    $html .= '</div><div class="iq-clear"></div>';

                }


            }


            $html .= '</div>';


        }


    } else {
        $html .= '<p>' . esc_attr__('Test not exists!', 'test-your-iq') . '</p>';
    }


} else {

    $html .= '<p>' . esc_attr__('Test ID is not set!', 'test-your-iq') . '</p>';

}
$html .= '<div class="iq-clear"></div></div></div>';



//Remove original canonical
if (function_exists('rel_canonical')) {
	remove_action('wp_head', 'rel_canonical');
}

//Remove Jatpack Open graph
add_filter('jetpack_enable_open_graph', '__return_false');

//Remove canonical Yoast Seo
add_filter('wpseo_canonical', '__return_false');
//Remove canonical All in one SEO
add_filter('aioseop_canonical_url', '__return_false');


add_filter('document_title_parts', 'pc_override_post_title', 10);