<?php
// Si ce fichier est appelé directement, avorte.
if (!defined('WPINC')) {
    die;
}

if (isset($_POST['konnect-update'])) {

    // Mettez à jour les options Konnect
    update_option('konnect-receiver-wallet-id', sanitize_text_field($_POST['konnect-receiver-wallet-id']));
    update_option('konnect-token', sanitize_text_field($_POST['konnect-token']));
    update_option('konnect-amount', sanitize_text_field($_POST['konnect-amount']));
    update_option('konnect-type', sanitize_text_field($_POST['konnect-type']));
    update_option('konnect-description', sanitize_text_field($_POST['konnect-description']));
    // Continuez pour les autres options Konnect

}

$konnect_receiver_wallet_id = get_option('konnect-receiver-wallet-id', '');
$konnect_token = get_option('konnect-token', '');
$konnect_amount = get_option('konnect-amount', '10000');
$konnect_type = get_option('konnect-type', 'immediate');
$konnect_description = get_option('konnect-description', 'Payment for something');
// Continuez pour les autres options Konnect

$currencies = TYIQ_TestYourIqAdmin::konnect_currency();

?>

<div class="wrap wp-admin">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>


    <form method="post" action="admin.php?page=<?php echo TYIQ_SLUG; ?>-paypal">

        <p>
        <h3><?php echo esc_attr__('Allow PayPal', 'test-your-iq'); ?> </h3>
        </p>
        <table class="wp-list-table widefat fixed media iq-test-paypal-aw" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th><?php echo esc_attr__('Yes', 'test-your-iq'); ?></th>
                <td><input type="radio" name="iq-allow-paypal" value="yes" <?php checked('yes', $iq_allow_paypal, $echo = TRUE); ?> /></td>
                <th><?php echo esc_attr__('No', 'test-your-iq'); ?></th>
                <td><input type="radio" name="iq-allow-paypal" value="no" <?php checked('no', $iq_allow_paypal, $echo = TRUE); ?>/></td>
            </tr>

            </tbody>
        </table>


        <p>
        <h3><?php echo esc_attr__('PayPal Client ID', 'test-your-iq'); ?></h3>
        </p>
        <p><?php echo esc_attr__('You must create an Live Business APP in PayPal for more information go to Info & Help section', 'test-your-iq'); ?> - <a href="<?php echo admin_url() . 'admin.php?page='.TYIQ_SLUG.'-help'; ?>"><?php echo esc_attr__('Info & Help', 'test-your-iq'); ?></a></p>
        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('Client ID', 'test-your-iq'); ?></th>
                <td><input type="text" name="iq-client-id" value="<?php echo esc_attr($iq_client_id); ?>" class="iq-large-text"/></td>

            </tr>
            </tbody>
        </table>

        <p>
        <h3><?php echo esc_attr__('Price', 'test-your-iq'); ?></h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('Amount', 'test-your-iq'); ?></th>
                <td><input type="number" name="iq-price" min="0" max="1000000" step=0.01 value="<?php echo esc_attr($iq_price); ?>"/></td>

            </tr>
            </tbody>
        </table>

        <p>
        <h3><?php echo esc_attr__('Currency', 'test-your-iq'); ?></h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('Select currency', 'test-your-iq'); ?></th>
                <td>
                    <select name="iq-currency">
                        <?php
                        foreach ($currencies as $currency => $currency_value) {
                            echo '<option ' . selected($currency, $iq_currency, FALSE) . ' value="' . esc_attr($currency) . '">' . $currency_value['label'] . '</option>';
                        }

                        ?>

                    </select>

                </td>

            </tr>
            </tbody>
        </table>


        <p>
        <h3><?php echo esc_attr__('Payment description', 'test-your-iq'); ?></h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('Payment description', 'test-your-iq'); ?></th>
                <td><input type="text" name="iq-pay-description" value="<?php echo $iq_pay_description; ?>" class="iq-large-text"/></td>
            </tr>
            </tbody>
        </table>


        <input type="hidden" name="iq-update" value="ok">
        <div class="iq-test-paypal-w900">
            <input type="submit" class="button iq-test-paypal-float" value="<?php echo esc_attr__('Save', 'test-your-iq'); ?>">
        </div>
        <div class="iq-clear iq-height-20"></div>


        <?php if($iq_client_id):  ?>
        <h2><?php echo esc_attr__('Test PayPal', 'test-your-iq'); ?></h2>

        <div id="smart-button-container">
            <div class="iq-align-left">
                <div id="paypal-button-container"></div>
            </div>
        </div>
        <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $iq_client_id; ?>&currency=<?php echo $iq_currency; ?>" data-sdk-integration-source="button-factory"></script>
        <script>
            function initPayPalButton() {
                paypal.Buttons({
                    style: {
                        shape: 'rect',
                        color: 'gold',
                        layout: 'vertical',
                        label: 'pay',

                    },

                    createOrder: function (data, actions) {
                        return actions.order.create({
                            purchase_units: [{"description": "<?php echo $iq_pay_description; ?>", "amount": {"currency_code": "<?php echo $iq_currency; ?>", "value": <?php echo $iq_price; ?>}}]
                        });
                    },

                    onApprove: function (data, actions) {
                        return actions.order.capture().then(function (details) {
                            alert('<?php echo esc_attr__('Your payment was successful!', 'test-your-iq'); ?>');
                        });
                    },

                    onError: function (err) {
                        console.log(err);
                    }
                }).render('#paypal-button-container');
            }

            initPayPalButton();
        </script>

        <?php endif;  ?>


        <h1><?php echo esc_attr__('Paypal page text', 'test-your-iq'); ?></h1>

        <p>
        <h3><?php echo esc_attr__('Short text above the headline', 'test-your-iq'); ?></h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('Short text', 'test-your-iq'); ?></th>
                <td><input type="text" name="iq-pay-short-text" value="<?php echo esc_attr($iq_pay_short_text); ?>" class="iq-large-text"/></td>
            </tr>
            </tbody>
        </table>

        <p>
        <h3><?php echo esc_attr__('The headline text', 'test-your-iq'); ?></h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('The headline', 'test-your-iq'); ?></th>
                <td><input type="text" name="iq-pay-headline" value="<?php echo esc_attr($iq_pay_headline); ?>" class="iq-large-text"/></td>
            </tr>
            </tbody>
        </table>

        <p>
        <h3><?php echo esc_attr__('The main description', 'test-your-iq'); ?></h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('The main description', 'test-your-iq'); ?></th>
                <td><textarea id="" name="iq-payment-description" rows="10" class="large-text iq-test-paypal-w900"><?php echo esc_attr($iq_payment_description); ?></textarea></td>
            </tr>
            </tbody>
        </table>

        <p>
        <h3><?php echo esc_attr__('Text for payment of the amount', 'test-your-iq'); ?></h3>
        </p>

        <table class="wp-list-table widefat fixed media iq-test-paypal-w900" cellspacing="0">
            <tbody id="the-list">
            <tr>
                <th width="25%"><?php echo esc_attr__('Amount text', 'test-your-iq'); ?></th>
                <td><input type="text" name="iq-pay-amount-text" value="<?php echo esc_attr($iq_pay_amount_text); ?>" class="iq-large-text"/></td>
            </tr>
            </tbody>
        </table>

        <div class="iq-test-paypal-w900">
            <input type="submit" class="button iq-test-paypal-float" value="<?php echo esc_attr__('Save', 'test-your-iq'); ?>">
        </div>

    </form>
</div>