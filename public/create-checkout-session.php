<?php

use Stripe\Checkout\Session;
use Stripe\Stripe;

require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// For sample support and debugging. Not required for production:
Stripe::setAppInfo(
    "stripe-samples/checkout-single-subscription",
    "0.0.3",
    "https://github.com/stripe-samples/checkout-single-subscription"
);

Stripe::setApiKey($_ENV[ 'STRIPE_SECRET_KEY' ]);

if ($_SERVER[ 'REQUEST_METHOD' ] !== 'POST') {
    echo 'Invalid request';
    exit;
}

$domain_url = $_ENV[ 'DOMAIN' ];

// Create new Checkout Session for the order
// Other optional params include:
// [billing_address_collection] - to display billing address details on the page
// [customer] - if you have an existing Stripe Customer ID
// [payment_intent_data] - lets capture the payment later
// [customer_email] - lets you prefill the email input in the form
// [automatic_tax] - to automatically calculate sales tax, VAT and GST in the checkout page
// For full details see https://stripe.com/docs/api/checkout/sessions/create

// ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID set as a query param
$checkout_session = Session::create([
    'success_url'                => $domain_url . '/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'                 => $domain_url . '/canceled.php',
    'payment_method_types'       => [ 'card' ],
    'mode'                       => 'subscription',
    'billing_address_collection' => 'required',
    'tax_id_collection'          => [
        'enabled' => true,
    ],
    'line_items'                 => [
        [
            'price'     => $_POST[ 'priceId' ],
            'quantity'  => 1,
            'tax_rates' => [
                'txr_1JouG9F46uGVJfc5vWIb67hi', // IVA 21% (ES)
            ],
        ]
    ],
    // 'automatic_tax' => ['enabled' => true],
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
