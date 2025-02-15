<?php
// Si ce fichier est appelé directement, avorte.
if (!defined('WPINC')) {
    die;
}

// Récupérer les variables de base
$konnect_receiver_wallet_id = get_option('konnect-receiver-wallet-id');
$konnect_token = get_option('konnect-token');
$konnect_amount = get_option('konnect-amount');
$konnect_type = get_option('konnect-type');
$konnect_description = get_option('konnect-description');
$konnect_accepted_payment_methods = get_option('konnect-accepted-payment-methods');
$konnect_lifespan = get_option('konnect-lifespan');
$konnect_checkout_form = get_option('konnect-checkout-form');
$konnect_add_payment_fees = get_option('konnect-add-payment-fees');
$konnect_first_name = get_option('konnect-first-name');
$konnect_last_name = get_option('konnect-last-name');
$konnect_phone_number = get_option('konnect-phone-number');
$konnect_email = get_option('konnect-email');
$konnect_order_id = get_option('konnect-order-id');
$konnect_webhook = get_option('konnect-webhook');
$konnect_silent_webhook = get_option('konnect-silent-webhook');
$konnect_success_url = get_option('konnect-success-url');
$konnect_fail_url = get_option('konnect-fail-url');
$konnect_theme = get_option('konnect-theme');

$ajax_url = admin_url('admin-ajax.php');

$html .= "<script>
function initKonnectButton() {
    var konnectConfig = {
        receiverWalletId: '$konnect_receiver_wallet_id',
        token: '$konnect_token',
        amount: $konnect_amount,
        type: '$konnect_type',
        description: '$konnect_description',
        acceptedPaymentMethods: $konnect_accepted_payment_methods,
        lifespan: $konnect_lifespan,
        checkoutForm: $konnect_checkout_form,
        addPaymentFeesToAmount: $konnect_add_payment_fees,
        firstName: '$konnect_first_name',
        lastName: '$konnect_last_name',
        phoneNumber: '$konnect_phone_number',
        email: '$konnect_email',
        orderId: '$konnect_order_id',
        webhook: '$konnect_webhook',
        silentWebhook: $konnect_silent_webhook,
        successUrl: '$konnect_success_url',
        failUrl: '$konnect_fail_url',
        theme: '$konnect_theme'
    };

    konnectAPIFunction(konnectConfig, function(response) {
        var payUrl = response.payUrl;

        // Affichez le bouton ou effectuez d'autres actions nécessaires
        // ...
    });
}

initKonnectButton();
</script>";

?>
