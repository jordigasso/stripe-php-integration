<?php

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

require_once 'shared.php';

try {
    // Fetch the Checkout Session to display the JSON result on the success page
    $checkout_session = Session::retrieve($_GET[ 'sessionId' ]);
    echo json_encode($checkout_session);
} catch (ApiErrorException $e) {
}

