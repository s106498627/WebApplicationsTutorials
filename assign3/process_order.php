<!-- Description: validates payment details and enters info to DB -->
<!-- Author: Charlie Hutcheson -->
<!-- Date: 2026-11-03 -->

<?php
session_start();
require_once("lib/helpers.php");
require_once("lib/validation.php");
require_once("lib/query.php");

// go back to enter the payment page again
function back_to_payment($errMsgs) {
    $_SESSION["payment_errors"] = $errMsgs;
    $_SESSION["payment_post"] = $_POST;
    header("location:payment.php");
    exit;
}

// make sure only form submissions can access this page. go to the start of the chain
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location:enquire.php");
    exit;
}

$errMsgs = [];
validate_payment($errMsgs);
if (count($errMsgs) > 0) {
    back_to_payment($errMsgs);
}

$customer = [
    "first_name" => get_post("first-name"),
    "last_name" => get_post("last-name"),
    "email" => get_post("email"),
    "street_address" => get_post("street-address"),
    "suburb" => get_post("suburb"),
    "state" => get_post("state"),
    "postcode" => get_post("postcode"),
    "phone" => get_post("phone"),
    "preferred_contact" => get_post("preferred-contact")
];

$card = [
    "card_type" => get_post("card-type"),
    "card_name" => get_post("card-name"),
    "card_number" => get_post("card-number"),
    "card_expiry" => get_post("card-expiry")
];

$items = [];
$itemCount = get_item_count();
for ($i = 1; $i <= $itemCount; $i++) {
    $items[] = [
        "product" => get_post("item-" . $i . "-product"),
        "capacity" => get_post("item-" . $i . "-capacity"),
        "quantity" => (int)get_post("item-" . $i . "-quantity")
    ];
}

// turn extras str into smth useful
$extras = get_post("extras") === "" ? [] : explode(",", get_post("extras")); // split CSVs into array

// vv send order to db vv
$conn = db_connect();
if (!$conn) {
    back_to_payment(["Failed to connect to the database."]); // show error to connect to db on the prev page
}


$orderId = create_order($conn, $customer, $card, $items, $extras, get_post("comment"));
mysqli_close($conn);
if ($orderId === false) { // failure in creating order entry
    back_to_payment(["Unable to create order."]);
}

// give order id to receipt so that only this user on this session can view it.
$_SESSION["receipt_order_id"] = $orderId;
header("location:receipt.php");
exit;
?>
