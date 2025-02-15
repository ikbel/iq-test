(function ($) {
    'use strict';

    $(function () {

        jQuery(document).on('click', '.iq-age-button', function () {

            var loading_gif = jQuery('.iq-test-button').data('loading');
            var age = jQuery(this).data('age');

            //Disable second click
            jQuery(this).fadeOut(300, function () {
                jQuery(this).find('.iq-jump-question').html('<img src="' + loading_gif + '" class="iq-loading-svg">');
                jQuery(this).fadeIn(300, function () {
                    get_age_test_data(age);
                });
            });

        });


        jQuery(document).on('click', '#create_new_test', function (event) {

            var loading_gif = jQuery('.iq-test-button').data('loading');
            var button = this;

            jQuery('.iq-test-button').fadeOut(300, function () {
                jQuery('#create_new_test').html('<img src="' + loading_gif + '" class="iq-loading-svg">');
                jQuery('.iq-test-button').fadeIn(300, function () {
                    get_new_test_data(button);
                });
            });


        });


        jQuery(document).on('click', '.gp-answer-link', function (event) {

            var loading_gif = jQuery('.iq-test-button').data('loading');
            var answer = this;

            //Disable second click
            jQuery('.answer-link-image, .iq-questions').fadeTo(300, 0.6).removeClass('gp-answer-link');
            jQuery('.iq-leave_question').fadeOut(300, function () {
                jQuery('.iq-jump-question').html('<img src="' + loading_gif + '" class="iq-loading-svg">');
                jQuery('.iq-leave_question').fadeIn(300, function () {
                    get_question_data(answer);
                });

            });


        });

        function get_question_data(answer) {

            var id = jQuery(answer).data('id');
            var testid = jQuery(answer).data('test');
            var current = jQuery(answer).data('current');
            var page = jQuery(answer).data('page');
            var link = jQuery(answer).attr('href');
            var devmode = jQuery('#iq-info-data-bar').data('devmod');

            var data = {
                action: 'add_test_answer',
                testid: testid,
                current: current,
                id: id,
                page_id: page,
                question: current + 1,
            };
            $.post(ajaxurl, data, function (data) {
                $('div.iq-test-wrap').fadeOut('300', function () {
                    $('div.iq-test-wrap').replaceWith(data);
                    if (devmode === 1) {
                        window.history.pushState({}, document.title, link);
                    }
                });

            });

        }

        function get_new_test_data(answer) {

            var testid = jQuery(answer).data('test');
            var link = jQuery(answer).attr('href');
            var page = jQuery(answer).data('page');
            var starttime = moment().format('YYYY-MM-DD HH:mm:ss');
            var devmode = jQuery('#iq-info-data-bar').data('devmod');

            var data = {
                action: 'new_iq_test',
                testid: testid,
                starttime: starttime,
                question: '1',
                page_id: page

            };
            $.post(ajaxurl, data, function (data) {

                $('div.iq-test-wrap-new').fadeOut('300', function () {
                    $('div.iq-test-wrap-new').replaceWith(data);
                    if (devmode === 1) {
                        window.history.pushState({}, document.title, link);
                    }
                });
            });

        }

        function get_age_test_data(age) {
            var rel = jQuery('#user_age_location').val();
            var testid = jQuery('#test_id').val();
            var endtime = moment().format('YYYY-MM-DD HH:mm:ss');

            var data = {
                action: 'set_age',
                age: age,
                testid: testid,
                endtime: endtime,
                iqresults: testid,
                permalink: rel
            };

            $.post(ajaxurl, data, function (data) {
                $('div.iq-test-wrap').fadeOut('300', function () {
                    $('div.iq-test-wrap').replaceWith(data);
                    window.history.pushState(null, '', location.href.split('?')[0] + '?iqresults=' + testid + '#iqtop');
                });
            });
        }


    });

}(jQuery));