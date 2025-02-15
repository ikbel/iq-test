<?php
global $wpdb;


if (isset($_POST['result-count'])) {
    update_option('iq-test-results-count', $_POST['result-count']);
}

$result_count = get_option('iq-test-results-count', 20);

$current_page = (isset($_GET['pagination']) ? (int)sanitize_text_field($_GET['pagination']) : 1); // current page in pagination
if ($current_page < 1)
    $current_page = 1;
$current_page -= 1; // first page have to be 0


$results_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "gp_iq_test");
$pages = $results_count / $result_count; // all pages count, we can use it in HTML pagination
$limit = ($current_page * $result_count) . "," . $result_count;
$next_page = $current_page + 2;
$prev_page = ($current_page == 1 ? "" : $current_page);

if (isset($_GET['iq-search'])) {
    $get_search = sanitize_text_field($_GET['iq-search']);
} else {
    $get_search = '';
}

if (isset($_GET['orderby'])) {
    $get_order = sanitize_text_field($_GET['orderby']);
    $pagination_parameter = '&orderby=' . $get_order;
} else {
    $get_order = '';
    $pagination_parameter = '';
}

//Delete selected tests
if (isset($_POST['delete-selected']) && isset($_POST['check_list']) && $_POST['check_list']) {
    foreach ($_POST['check_list'] as $test_id) {
        $sql = "DELETE FROM " . $wpdb->prefix . "gp_iq_test WHERE test_id = '" . $test_id . "'";
        $sql_2 = "DELETE FROM " . $wpdb->prefix . "gp_iq_test_data WHERE test_id = '" . $test_id . "'";
        $wpdb->query($sql);
        $wpdb->query($sql_2);
    }
}

//Mark selected as paid
if (isset($_POST['mark-selected-paid']) && isset($_POST['check_list']) && $_POST['check_list']) {
    foreach ($_POST['check_list'] as $test_id) {
        $today = current_time('mysql');
        $data = [
            'payment_date' => $today,
            'payment' => 'yes'
        ];
        $wpdb->update(
            $wpdb->prefix . 'gp_iq_test',
            $data,
            ['test_id' => $test_id]);

    }
}

//Mark selected as unpaid
if (isset($_POST['mark-selected-unpaid']) && isset($_POST['check_list']) && $_POST['check_list']) {
    foreach ($_POST['check_list'] as $test_id) {
        $data = [
            'payment' => 'no'
        ];
        $wpdb->update(
            $wpdb->prefix . 'gp_iq_test',
            $data,
            ['test_id' => $test_id]);

    }
}

//Delete unfinished tests
if (isset($_POST['delete-unfinished'])) {

    $get_all_test_id = $wpdb->get_results("SELECT test_id FROM " . $wpdb->prefix . "gp_iq_test  WHERE finish = 'no' AND start < DATE_ADD( NOW(), INTERVAL -24 HOUR )");

    $all_tests = count($get_all_test_id);

    if ($all_tests >= 1) {

        $sql = "DELETE FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'no' AND start < DATE_ADD( NOW(), INTERVAL -24 HOUR )";
        $wpdb->query($sql);

        foreach ($get_all_test_id as $test_id) {

            $sql_2 = "DELETE FROM " . $wpdb->prefix . "gp_iq_test_data WHERE test_id = '" . $test_id->test_id . "' ";
            $wpdb->query($sql_2);
        }

    }

    wp_redirect(admin_url('admin.php?page=test-your-iq-setting&orderby=unfinished'));

}

//Delete unpaid tests
if (isset($_POST['delete-unpaid'])) {

    $get_all_test_id = $wpdb->get_results("SELECT test_id FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'yes' AND payment= 'no' AND start < DATE_ADD( NOW(), INTERVAL -24 HOUR )");

    $all_tests = count($get_all_test_id);

    if ($all_tests >= 1) {

        $sql = "DELETE FROM " . $wpdb->prefix . "gp_iq_test  WHERE finish = 'yes' AND payment= 'no'";
        $wpdb->query($sql);

        foreach ($get_all_test_id as $test_id) {

            $sql_2 = "DELETE FROM " . $wpdb->prefix . "gp_iq_test_data WHERE test_id = '" . $test_id->test_id . "' ";
            $wpdb->query($sql_2);
        }

    }

    wp_redirect(admin_url('admin.php?page=test-your-iq-setting'));

}

//Delete all tests
if (isset($_POST['delete-all'])) {

    $sql = "DELETE FROM " . $wpdb->prefix . "gp_iq_test";
    $wpdb->query($sql);

    $sql_2 = "DELETE FROM " . $wpdb->prefix . "gp_iq_test_data";
    $wpdb->query($sql_2);

    wp_redirect(admin_url('admin.php?page=test-your-iq-setting'));
}


// limit [offset], [limit]

if (isset($_GET['orderby'])) {
    if ($get_order == 'newest') {
        //Get tests
        $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'yes' ORDER BY start DESC LIMIT $limit");
    } elseif ($get_order == 'oldest') {
        //Get tests
        $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'yes' ORDER BY start ASC LIMIT $limit");
    } elseif ($get_order == 'pointup') {
        //Get tests
        $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'yes' ORDER BY result DESC LIMIT $limit");
    } elseif ($get_order == 'pointdown') {
        //Get tests
        $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'yes' ORDER BY result ASC LIMIT $limit");
    } elseif ($get_order == 'unfinished') {
        //Get tests
        $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'no' ORDER BY start DESC LIMIT $limit");
    } elseif ($get_order == 'paid') {
        //Get tests
        $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE payment = 'yes' ORDER BY start DESC LIMIT $limit");
    } elseif ($get_order == 'unpaid') {
        //Get tests
        $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE payment = 'no' ORDER BY start DESC LIMIT $limit");
    }
} elseif (isset($_GET['iq-search'])) {
    $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE test_id='" . $get_search . "'");
} else {
    //Get tests
    $tests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "gp_iq_test WHERE finish = 'yes' ORDER BY start DESC LIMIT $limit");
}


$items = count($tests);

$blogurl = home_url();
?>

<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>


    <form method="get" action="" class="iq-tests-form">

        <input type="hidden" name="page" value="<?php echo TYIQ_SLUG; ?>-setting">

        <select name="orderby" id="iq-orderby" onchange="javascript:this.form.submit()">
            <option <?php selected('newest', $get_order, TRUE); ?> value="newest"><?php echo esc_attr__('Newest', 'test-your-iq'); ?></option>
            <option <?php selected('oldest', $get_order, TRUE); ?> value="oldest"><?php echo esc_attr__('Oldest', 'test-your-iq'); ?></option>
            <option <?php selected('pointup', $get_order, TRUE); ?>value="pointup"><?php echo esc_attr__('Most Points', 'test-your-iq'); ?></option>
            <option <?php selected('pointdown', $get_order, TRUE); ?> value="pointdown"><?php echo esc_attr__('Least Points', 'test-your-iq'); ?></option>
            <option disabled>--------------------</option>
            <option <?php selected('paid', $get_order, TRUE); ?> value="paid"><?php echo esc_attr__('Paid tests', 'test-your-iq'); ?></option>
            <option <?php selected('unpaid', $get_order, TRUE); ?> value="unpaid"><?php echo esc_attr__('Unpaid tests', 'test-your-iq'); ?></option>
            <option disabled>--------------------</option>
            <option <?php selected('unfinished', $get_order, TRUE); ?> value="unfinished"><?php echo esc_attr__('Unfinished tests', 'test-your-iq'); ?></option
        </select>

        <input type="submit" id="doaction" class="button action" value="<?php echo esc_attr__('Filter', 'test-your-iq'); ?>">

    </form>

    <form method="get" action="" class="iq-tests-form">

        <input type="hidden" name="page" value="<?php echo TYIQ_SLUG; ?>-setting">

        <input type="text" name="iq-search" value="" placeholder="<?php echo esc_attr__('Search by Test ID', 'test-your-iq'); ?>" class="regular-text"/>
        <input type="submit" id="doaction2" class="button action" value="<?php echo esc_attr__('Search', 'test-your-iq'); ?>">
    </form>

    <form method="post" action="" class="iq-tests-form">

        <input type="number" name="result-count" min="10" value="<?php echo esc_attr($result_count) ?>" class="small-text"/>

        <input type="submit" id="doaction3" class="button action" value="<?php echo esc_attr__('Results per page', 'test-your-iq'); ?>">

    </form>

    <form method="post" action="" id="iq-tests">

        <table class="wp-list-table widefat fixed media iq-margin-top-5" cellspacing="0">
            <thead>
            <tr>
                <th class="manage-column" width="50"><input id="iq-select-all" type="checkbox" name="select_all" value="1"></th>
                <th class="manage-column" width="50"><?php echo esc_attr__('ID', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Test ID', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Date', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Time', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Result', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Finished', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Payment', 'test-your-iq'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th class="manage-column"><input id="iq-select-all2" type="checkbox" name="select_all" value="1"></th>
                <th class="manage-column"><?php echo esc_attr__('ID', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Date', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Test ID', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Time', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Result', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Finished', 'test-your-iq'); ?></th>
                <th class="manage-column"><?php echo esc_attr__('Payment', 'test-your-iq'); ?></th>
            </tr>
            </tfoot>
            <tbody id="the-list">
            <?php
            if (!empty($tests)) {
                foreach ($tests as $item) {
                    if ($item->payment == 'yes') {
                        $paymen_date = ' (' . date(get_option('date_format') . " - " . get_option('time_format'), strtotime($item->payment_date)) . ')';
                    } else {
                        $paymen_date = '';
                    }

                    if ($item->payment == 'yes') {
                        $payment = '<span class="iq-color-green">' . esc_attr__('Yes', 'test-your-iq') . '</span>';
                    } else {
                        $payment = '<span class="iq-color-black">' . esc_attr__('No', 'test-your-iq') . '</span>';
                    }

                    $date = date(get_option('date_format') . " - " . get_option('time_format'), strtotime($item->start));

                    if ($item->finish == 'yes') {
                        $finish_text = esc_attr__('Yes', 'test-your-iq');
                    } else {
                        $finish_text = esc_attr__('No', 'test-your-iq');
                    }

                    ?>
                    <tr>
                        <td><input class="iq-check-this" type="checkbox" name="check_list[]" value="<?php echo $item->test_id; ?>"></td>
                        <td><?php echo $item->id; ?></td>
                        <td><?php echo $item->test_id; ?></td>
                        <td><?php echo $date; ?></td>
                        <td><?php echo $item->time; ?></td>
                        <td>IQ <?php echo $item->result; ?></td>
                        <td><?php echo $finish_text; ?></td>
                        <td><?php echo $payment; ?><?php echo $paymen_date; ?></td>

                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>

        <div class="iq-clear"></div>

        <div class="iq-text-bold iq-margin-top-5">


            <?php if ($current_page >= 1) { ?>

                <a href="<?php echo admin_url('admin.php?page=test-your-iq-setting'); ?><?php echo $pagination_parameter; ?>&pagination=<?php echo $prev_page ?>" class="btn btn-info"> < </a>

            <?php }
            if ($result_count < $results_count and !isset($_GET['pagination']) && $items >= $result_count) { ?>
                <a href="<?php echo admin_url('admin.php?page=test-your-iq-setting'); ?><?php echo $pagination_parameter; ?>&pagination=<?php echo $next_page ?>" class="btn btn-info iq-float-right"> > </a>
            <?php }
            if ($result_count < $results_count and isset($_GET['pagination']) and $_GET['pagination'] < $pages && $items >= $result_count) {
                ?>
                <a href="<?php echo admin_url('admin.php?page=test-your-iq-setting'); ?><?php echo $pagination_parameter; ?>&pagination=<?php echo $next_page ?>" class="btn btn-info iq-float-right"> > </a>

            <?php } ?>


        </div>

        <div class="iq-clear"></div>


        <input type="submit" onclick="if (!confirm('<?php echo esc_attr__('Do you want to delete all selected tests?', 'test-your-iq'); ?>')) return false;" name="delete-selected" class="button iq-button-tests" value="<?php echo esc_attr__('Delete selected', 'test-your-iq'); ?>">
        <input type="submit" onclick="if (!confirm('<?php echo esc_attr__('Do you want mark selected tests as paid?', 'test-your-iq'); ?>')) return false;" name="mark-selected-paid" class="button iq-button-tests" value="<?php echo esc_attr__('Mark selected as paid', 'test-your-iq'); ?>">
        <input type="submit" onclick="if (!confirm('<?php echo esc_attr__('Do you want mark selected tests as unpaid?', 'test-your-iq'); ?>')) return false;" name="mark-selected-unpaid" class="button iq-button-tests" value="<?php echo esc_attr__('Mark selected as unpaid', 'test-your-iq'); ?>">
        <br><br>
        <input type="submit" onclick="if (!confirm('<?php echo esc_attr__('Do you want to delete all unfinished tests? Are you sure!?', 'test-your-iq'); ?>')) return false;" name="delete-unfinished" class="button iq-button-tests" value="<?php echo esc_attr__('Delete all unfinished tests older than 24 hours!', 'test-your-iq'); ?>*">
        <input type="submit" onclick="if (!confirm('<?php echo esc_attr__('Do you want to delete all unpaid tests? Are you sure!?', 'test-your-iq'); ?>')) return false;" name="delete-unpaid" class="button iq-button-tests" value="<?php echo esc_attr__('Delete all unpaid tests older than 24 hours!', 'test-your-iq'); ?>**">
        <input type="submit" onclick="if (!confirm('<?php echo esc_attr__('Do you want to delete all tests? Are you sure!?', 'test-your-iq'); ?>')) return false;" name="delete-all" class="button iq-button-tests-2" value="<?php echo esc_attr__('Delete all tests results!', 'test-your-iq'); ?>***">


        <div class="iq-clear"></div>

        <p class="iq-font-14px"><strong>* <?php echo esc_attr__('Delete all unfinished tests older than 24 hours!', 'test-your-iq'); ?></strong> - <?php echo esc_attr__('It will clear your database from unnecessary data and your running tests are safe because of the 24 hour limit.', 'test-your-iq'); ?></p>
        <p class="iq-font-14px"><strong>** <?php echo esc_attr__('Delete all unpaid tests older than 24 hours!', 'test-your-iq'); ?></strong> - <?php echo esc_attr__('It will delete all tests that are finished but not paid.', 'test-your-iq'); ?></p>
        <p class="iq-font-14px"><strong>*** <?php echo esc_attr__('Delete all tests results!', 'test-your-iq'); ?></strong> - <?php echo esc_attr__('It will delete all tests and from database so please be sure that you do not have any running tests!', 'test-your-iq'); ?></p>

    </form>
</div>


