<?php
session_start();
require_once("lib/helpers.php");
require_once("lib/validation.php");
require_once("lib/query.php");

// go back to enter the enquire page again
function back_to_enquire($errMsgs) {
    $_SESSION["enquire_errors"] = $errMsgs;
    $_SESSION["enquire_post"] = $_POST; // sending exactly what was given to me
    header("location:enquire.php");
    exit;
}

// check vals from enquire are correct before loading the page
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $enquireErrors = [];
    validate_enquire($enquireErrors);
    if (count($enquireErrors) > 0) {
        back_to_enquire($enquireErrors); // go back and fix the errors
    }
}

// get back fields and errors from process_order
$paymentErrors = isset($_SESSION["payment_errors"]) ? $_SESSION["payment_errors"] : [];
$posted = isset($_SESSION["payment_post"]) ? $_SESSION["payment_post"] : [];

// unbind for next round of validation
unset($_SESSION["payment_errors"]);
unset($_SESSION["payment_post"]);

// prices for the order summary, same as the cart shows
$conn = db_connect();
$prices = load_product_prices($conn);
$extraPrices = load_extra_prices($conn);
if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Discover the very best in flash storage at Sandish">
    <meta name="keywords" content="Sandish, Flash, Storage, USB">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/style-form.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <script src="scripts/part2.js"></script>
    <script src="scripts/enhancements.js"></script>
    <title>Payment</title>
</head>

<body>
    <?php include_once("components/navbar.inc.php") ?>

    <main>
        <br>
        <h1>Payment</h1>

        <!-- redirect to enquire page when nothing is in localStorage -->
        <p id="no-order" class="error-box" hidden>
            No order found. Please <a href="enquire.php">complete the order form</a> first.
        </p>

        <!-- BEGIN Order summary -->
        <section id="order-summary" class="summary">
            <h2>Your details</h2>
            <dl class="definition">
                <dt>Name</dt>
                <dd id="confirm-name"></dd>
                <dt>Email</dt>
                <dd id="confirm-email"></dd>
                <dt>Phone</dt>
                <dd id="confirm-phone"></dd>
                <dt>Billing address</dt>
                <dd id="confirm-address"></dd>
                <dt>Preferred contact</dt>
                <dd id="confirm-contact"></dd>
                <dt>Comment</dt>
                <dd id="confirm-comment"></dd>
            </dl>

            <h2>Your order</h2>
            <!-- wide tables scroll sideways if too small -->
            <div class="table-scroll">
                <!-- passing price form the db to my jsavascript cart... not rewriting the cart too much effort :( -->
                <table id="cart-summary" class="cart-table" data-prices="<?php echo htmlspecialchars(json_encode($prices)) ?>"
                    data-extra-prices="<?php echo htmlspecialchars(json_encode($extraPrices)) ?>">
                    <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Storage</th>
                            <th scope="col">Unit price</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Line total</th>
                        </tr>
                    </thead>
                    <!-- one row per cart item -->
                    <tbody id="cart-rows"></tbody>
                    <tfoot>
                        <tr>
                            <th scope="row" colspan="4">Order extras</th>
                            <td id="confirm-extras"></td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="4">Total</th>
                            <td id="confirm-total" class="total"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
        <!-- END Order summary -->

        <!-- NOTE: values in this form are re-populated if there were any errors from the posted values -->
        <form id="payment-form" action="process_order.php" method="post"
            novalidate>
            <!-- hide the personal details so that they can be sent with card details -->
            <input type="hidden" id="h-first-name" name="first-name">
            <input type="hidden" id="h-last-name" name="last-name">
            <input type="hidden" id="h-email" name="email">
            <input type="hidden" id="h-street-address" name="street-address">
            <input type="hidden" id="h-suburb" name="suburb">
            <input type="hidden" id="h-state" name="state">
            <input type="hidden" id="h-postcode" name="postcode">
            <input type="hidden" id="h-phone" name="phone">
            <input type="hidden" id="h-preferred-contact" name="preferred-contact">
            <!-- cart items here from js -->
            <div id="h-cart-items" hidden></div>
            <input type="hidden" id="h-extras" name="extras">
            <input type="hidden" id="h-comment" name="comment">

            <div class="form-col">
                <fieldset>
                    <legend>Card details</legend>
                    <div>
                        <label for="card-type">Card type</label>
                        <!-- no valid default: the placeholder option has an empty value -->
                        <select id="card-type" name="card-type">
                            <option value="" selected>-- Choose a card --</option>
                            <option value="visa"<?php echo get_selected("card-type", $posted, "visa") ?>>Visa</option>
                            <option value="mastercard"<?php echo get_selected("card-type", $posted, "mastercard") ?>>Mastercard</option>
                            <option value="americanExpress"<?php echo get_selected("card-type", $posted, "americanExpress") ?>>American Express</option>
                        </select>
                    </div>
                    <div>
                        <label for="card-name">Name on card</label>
                        <div class="field">
                            <input type="text" id="card-name" name="card-name" value="<?php echo get("card-name", $posted) ?>">
                        </div>
                    </div>
                    <div>
                        <label for="card-number">Card number</label>
                        <div class="field">
                            <input type="text" id="card-number" name="card-number" value="<?php echo get("card-number", $posted) ?>" inputmode="numeric">
                        </div>
                    </div>
                    <div>
                        <label for="card-expiry">Expiry (MM-YY)</label>
                        <div class="field">
                            <input type="text" id="card-expiry" name="card-expiry" value="<?php echo get("card-expiry", $posted) ?>" placeholder="MM-YY">
                        </div>
                    </div>
                    <div>
                        <label for="card-cvv">CVV</label>
                        <div class="field">
                            <input type="text" id="card-cvv" name="card-cvv" inputmode="numeric" value="<?php echo get("card-cvv", $posted) ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- combined validation messages are written here by scripts/part2.js -->
                <?php echo create_err_box($paymentErrors) ?>

                <div class="button-row">
                    <button type="submit" id="check-out" class="button-link">Check Out</button>
                    <button type="button" id="cancel-order" class="button-link button-secondary">Cancel Order</button>
                </div>
            </div>
        </form>
    </main>
    
    <?php include_once("components/footer.inc.php") ?>
</body>

</html>
