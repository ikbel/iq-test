<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


if (isset($_POST['update'])) {


    if (isset($_POST['result_type'])) {
        update_option('iq-result_type', sanitize_text_field($_POST['result_type']));
    }
    if (isset($_POST['button_type'])) {
        update_option('iq-button_type', sanitize_text_field($_POST['button_type']));
    }
    if (isset($_POST['first_text'])) {
        update_option('iq-first_text', sanitize_text_field($_POST['first_text']));
    }
    if (isset($_POST['second_text'])) {
        update_option('iq-second_text', sanitize_text_field($_POST['second_text']));
    }
    if (isset($_POST['third_text'])) {
        update_option('iq-third_text', sanitize_text_field($_POST['third_text']));
    }
    if (isset($_POST['button_color'])) {
        update_option('iq-button_color', sanitize_text_field($_POST['button_color']));
    }
    if (isset($_POST['allow_social'])) {
        update_option('iq-allow_social', sanitize_text_field($_POST['allow_social']));
    }
    if (isset($_POST['og-image'])) {
        update_option('iq-og_image', sanitize_text_field($_POST['og-image']));
    }
    if (isset($_POST['og-image-url'])) {
        update_option('iq-og_image_url', sanitize_text_field($_POST['og-image-url']));
    }

    $social_icons = [];

    if (isset($_POST['facebook'])) {
        $social_icons['facebook'] = '1';
    }else{
        $social_icons['facebook'] = '0';
    }

    if (isset($_POST['twitter'])) {
        $social_icons['twitter'] = '1';
    }else{
        $social_icons['twitter'] = '0';
    }

    if (isset($_POST['pinterest'])) {
        $social_icons['pinterest'] = '1';
    }else{
        $social_icons['pinterest'] = '0';
    }

    if (isset($_POST['whatsapp'])) {
        $social_icons['whatsapp'] = '1';
    }else{
        $social_icons['whatsapp'] = '0';
    }

    if (isset($_POST['vkontakte'])) {
        $social_icons['vkontakte'] = '1';
    }else{
        $social_icons['vkontakte'] = '0';
    }

    update_option('iq-allow_social-icons', $social_icons);
}
$text = TYIQ_TestYourIqAdmin::get_basic_texts();

$result_type = get_option('iq-result_type', 'points');
$button_type = get_option('iq-button_type', 'withboxes');
$first_text = get_option('iq-first_text', $text['top']);
$second_text = get_option('iq-second_text', $text['left']);
$third_text = get_option('iq-third_text', $text['right']);
$button_color = get_option('iq-button_color', '#FF9900');
$allow_social = get_option('iq-allow_social', 'yes');
$social_icons = get_option('iq-allow_social-icons', ['facebook' => 1, 'twitter' => 1, 'pinterest' => 1, 'whatsapp' => 1, 'vkontakte' => 1]);
$og_image = get_option('iq-og_image', 'default');
$og_image_url = get_option('iq-og_image_url', '');



?>

<div class="wrap" class="iq-test-paypal-w900">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <p>
    <h3><?php echo esc_attr__('Shortcode', 'test-your-iq'); ?>  </h3>
    </p>

    <table class="wp-list-table widefat fixed media iq-test-paypal-aw" cellspacing="0">
        <tbody id="the-list">
        <tr>
            <th><?php echo esc_attr__('You can add the IQ Test to any post or page using this shortcode', 'test-your-iq'); ?>:</th>
            <td><span class="iq-shortcode-span">[test-your-iq]</span>&nbsp</td>


        </tr>
        </tbody>
    </table>

    <p>
    <h3><?php echo esc_attr__('Select view test result!', 'test-your-iq'); ?>  </h3>
    </p>
    <form method="post" action="admin.php?page=<?php echo TYIQ_SLUG; ?>">
        <table class="wp-list-table widefat fixed media iq-test-paypal-aw" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th><?php echo esc_attr__('Internacional Stanford-Binet Scale (points)', 'test-your-iq'); ?></th>
                <td><input type="radio" name="result_type" value="points" <?php if ($result_type == 'points') {
                        echo 'checked="checked"';
                    } ?> <?php if ($result_type == '') {
                        echo 'checked="checked"';
                    } ?>/></td>
                <th><?php echo esc_attr__('Percentil Scale (%)', 'test-your-iq'); ?></th>
                <td><input type="radio" name="result_type" value="percent" <?php if ($result_type == 'percent') {
                        echo 'checked="checked"';
                    } ?> /></td>

            </tr>
            </tbody>
        </table>

        <p>
        <h3><?php echo esc_attr__('Start Button', 'test-your-iq'); ?>  </h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-aw" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th><?php echo esc_attr__('Simple - without boxes', 'test-your-iq'); ?></th>
                <td><input type="radio" name="button_type" value="simple" <?php if ($button_type == 'simple') {
                        echo 'checked="checked"';
                    } ?> <?php if ($button_type == '') {
                        echo 'checked="checked"';
                    } ?>/></td>
                <th><?php echo esc_attr__('Alternative - With 3 text boxes', 'test-your-iq'); ?></th>
                <td><input type="radio" name="button_type" value="withboxes" <?php if ($button_type == 'withboxes') {
                        echo 'checked="checked"';
                    } ?> /></td>

            </tr>
            </tbody>
        </table>

        <p>
        <h3><?php echo esc_attr__('Button, lines and progress bar color', 'test-your-iq'); ?>  </h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-aw" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th><?php echo esc_attr__('Select color', 'test-your-iq'); ?></th>
                <td><input type="color" name="button_color" value="<?php echo esc_attr($button_color); ?>"/></td>
            </tr>
            </tbody>
        </table>


        <p>
        <h3><?php echo esc_attr__('Top Text in the box', 'test-your-iq'); ?></h3>
        </p>
        <p>
            <?php
            $editor_id = 'first_text';
            wp_editor(stripslashes($first_text), strtolower($editor_id), [
                'wpautop' => true,
                'quicktags' => true,
                'textarea_rows' => 10,

            ]);
            ?>
        </p>
        <p>
        <h3><?php echo esc_attr__('Left Side Text in the box', 'test-your-iq'); ?>  </h3>
        </p>
        <p>
            <?php
            $editor_id = 'second_text';
            wp_editor(stripslashes($second_text), strtolower($editor_id), [
                'wpautop' => true,
                'quicktags' => true,
                'textarea_rows' => 10,
            ]);
            ?>
        </p>
        <p>
        <h3><?php echo esc_attr__('Right Side Text in the box', 'test-your-iq'); ?>  </h3>
        </p>
        <p>
            <?php
            $editor_id = 'third_text';
            wp_editor(stripslashes($third_text), strtolower($editor_id), [
                'wpautop' => true,
                'quicktags' => true,
                'textarea_rows' => 10,
            ]);
            ?>
        </p>

        <p>
        <h3><?php echo esc_attr__('Allow social icons for sharing in the result page', 'test-your-iq'); ?>  </h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-aw" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th><?php echo esc_attr__('Yes', 'test-your-iq'); ?></th>
                <td><input type="radio" name="allow_social" value="yes" <?php if ($allow_social == 'yes') {
                        echo 'checked="checked"';
                    } ?> /></td>
                <th><?php echo esc_attr__('No', 'test-your-iq'); ?></th>
                <td><input type="radio" name="allow_social" value="no" <?php if ($allow_social == 'no') {
                        echo 'checked="checked"';
                    } ?> /></td>

            </tr>
            <tr>
                <th><?php echo esc_attr__('Facebook', 'test-your-iq'); ?></th>
                <td colspan="3"><input type="checkbox" name="facebook" value="1" <?php checked($social_icons['facebook'], '1', TRUE);; ?>></td>
            </tr>
            <tr>
                <th><?php echo esc_attr__('Twitter', 'test-your-iq'); ?></th>
                <td colspan="3"><input type="checkbox" name="twitter" value="1" <?php checked($social_icons['twitter'], '1', TRUE);; ?>></td>
            </tr>
            <tr>
                <th><?php echo esc_attr__('Pinterest', 'test-your-iq'); ?></th>
                <td colspan="3"><input type="checkbox" name="pinterest" value="1" <?php checked($social_icons['pinterest'], '1', TRUE);; ?>></td>
            </tr>
            <tr>
                <th><?php echo esc_attr__('Whatsapp', 'test-your-iq'); ?></th>
                <td colspan="3"><input type="checkbox" name="whatsapp" value="1" <?php checked($social_icons['whatsapp'], '1', TRUE);; ?>></td>
            </tr>

                <th><?php echo esc_attr__('VKontakte', 'test-your-iq'); ?></th>
                <td colspan="3"><input type="checkbox" name="vkontakte" value="1" <?php checked($social_icons['vkontakte'], '1', TRUE);; ?>></td>
            </tr>
            </tbody>
        </table>


        <p>
        <h3><?php echo esc_attr__('Image for Open Graph', 'test-your-iq'); ?>  </h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w600" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th><?php echo esc_attr__('Use default image', 'test-your-iq'); ?></th>
                <td><input type="radio" name="og-image" value="default" <?php if ($og_image == 'default') {
                        echo 'checked="checked"';
                    } ?> /></td>
                <th><?php echo esc_attr__('Use custom image', 'test-your-iq'); ?></th>
                <td><input type="radio" name="og-image" value="own" <?php if ($og_image == 'own') {
                        echo 'checked="checked"';
                    } ?> /></td>


            </tr>
            <tr>
                <th><?php echo esc_attr__('Custom image URL', 'test-your-iq'); ?></th>
                <td colspan="3"><input type="url" name="og-image-url" value="<?php echo $og_image_url; ?>" class="iq-large-text"></td>

            </tr>
            </tbody>
        </table>


        <input type="hidden" name="update" value="ok">
        <input type="submit" class="button iq-test-paypal-float" value="<?php echo esc_attr__('Save', 'test-your-iq'); ?>">

    </form>


</div>