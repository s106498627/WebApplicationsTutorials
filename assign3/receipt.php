<?php
// Description: displays info saved to DB about the order
// Author: Charlie Hutcheson
// Date: 2026-11-03

session_start();
require_once("lib/helpers.php");
require_once("lib/query.php");

// only allow the user to view the receipt from their own session
if (!isset($_SESSION["receipt_order_id"])) {
    header("location:enquire.php");
    exit;
}

$conn = db_connect();

// outside scope so order can be seen globally to this module/file
$order = false;
if ($conn) {
    $order = get_order($conn, $_SESSION["receipt_order_id"]);
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
    <title>Receipt</title>
</head>

<body>
    <?php include_once("components/navbar.inc.php") ?>

    <main>
        <br>
        <h1>Receipt</h1>

        <?php if ($order === false) { ?>
            <p class="error-box">Unable to load your receipt.</p>
        <?php } else { ?>
        <!-- BEGIN Receipt -->
        <section id="receipt" class="summary">
            <p>Thank you for your order, <?php echo $order["first_name"] ?>!</p>

            <h2>Order</h2>
            <dl class="definition">
                <dt>Order number</dt>
                <dd><?php echo $order["order_id"] ?></dd>
                <dt>Status</dt>
                <dd><?php echo $order["order_status"] ?></dd>
                <dt>Ordered at</dt>
                <dd><?php echo $order["order_time"] ?></dd>
            </dl>

            <h2>Your details</h2>
            <dl class="definition">
                <dt>Customer number</dt>
                <dd><?php echo $order["customer_id"] ?></dd>
                <dt>Name</dt>
                <dd><?php echo $order["first_name"] . " " . $order["last_name"] ?></dd>
                <dt>Email</dt>
                <dd><?php echo $order["email"] ?></dd>
                <dt>Phone</dt>
                <dd><?php echo $order["phone"] ?></dd>
                <dt>Billing address</dt>
                <dd><?php echo $order["street_address"] . ", " . $order["suburb"] . ", " . $order["state"] . ", " . $order["postcode"] ?></dd>
                <dt>Preferred contact</dt>
                <dd><?php echo strtolower($order["preferred_contact"]) ?></dd>
                <dt>Comment</dt>
                <dd><?php echo $order["comment"] ?></dd>
            </dl>

            <h2>Payment</h2>
            <dl class="definition">
                <dt>Card type</dt>
                <dd><?php echo $order["card_type"] ?></dd>
                <dt>Name on card</dt>
                <dd><?php echo $order["card_name"] ?></dd>
                <dt>Card number</dt>
                <!-- only show the last 4 digits, no cvv -->
                <dd><?php echo "**** **** **** " . substr($order["card_number"], -4) ?></dd>
                <dt>Expiry</dt>
                <dd><?php echo $order["card_expiry"] ?></dd>
            </dl>

            <h2>Your order</h2>
            <!-- wide tables scroll sideways if too small -->
            <div class="table-scroll">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Storage</th>
                            <th scope="col">Unit price</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Line total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- construct item rows for the table -->
                        <?php foreach ($order["items"] as $item) { ?>
                        <tr>
                            <td><?php echo product_name($item["product"]) ?></td>
                            <td><?php echo strtoupper($item["capacity"]) ?></td>
                            <td>$<?php echo $item["price"] ?></td>
                            <td><?php echo $item["quantity"] ?></td>
                            <td>$<?php echo number_format($item["price"] * $item["quantity"], 2) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="row" colspan="4">Order extras</th>
                            <td>
                                <?php
                                if (count($order["extras"]) === 0) {
                                    echo "None";
                                }
                                foreach ($order["extras"] as $extra) {
                                    echo $extra["extra_name"] . " ($" . $extra["price"] . ")<br>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="4">Total</th>
                            <td class="total">$<?php echo $order["order_cost"] ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
        <!-- END Receipt -->
        <?php } ?>

        
        <a class="button-link main-margin" href="index.php">Back to home</a>
    </main>

    <?php include_once("components/footer.inc.php") ?>
</body>

</html>
