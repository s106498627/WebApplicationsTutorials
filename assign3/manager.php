<?php
// Description: Manage orders, change progress, cancel orders
// Author: Charlie Hutcheson
// Date: 2026-11-03

session_start();
require_once("lib/helpers.php");
require_once("lib/query.php");

// copy of the DB enum to send & update
$STATUSES = ["PENDING", "FULFILLED", "PAID", "ARCHIVED"];
$SORTS = [
    "date_desc" => "Newest first",
    "date_asc" => "Oldest first",
    "cost_desc" => "Total cost, highest first",
    "cost_asc" => "Total cost, lowest first"
];

// capture post from table rows order update/cancels
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = (int)get_post("order-id");
    $action = get_post("action");

    $conn = db_connect();
    if (!$conn) {
        $_SESSION["manager_message"] = "Failed to connect to the database.";
        $_SESSION["manager_message_err"] = "true";
    } else if ($action === "update") { // update the order status
        $status = get_post("status");
        if (update_order_status($conn, $orderId, $status)) {
            $_SESSION["manager_message"] = "Order " . $orderId . " set to " . strtolower($status) . "."; // success msg
        } else {
            $_SESSION["manager_message"] = "Unable to update order " . $orderId . "."; // fail message
            $_SESSION["manager_message_err"] = "true";
        }
    } else if ($action === "cancel") {
        if (cancel_order($conn, $orderId)) {
            $_SESSION["manager_message"] = "Order " . $orderId . " cancelled.";
        } else {
            $_SESSION["manager_message"] = "Unable to cancel order " . $orderId . ". Only pending orders can be cancelled.";
            $_SESSION["manager_message_err"] = "true";
        }
    }
    if ($conn) {
        mysqli_close($conn);
    }

    // refresh the page wit hthe same search criteria
    $search = isset($_SESSION["manager_search"]) ? $_SESSION["manager_search"] : "";
    header("location:manager.php" . ($search === "" ? "" : "?" . $search)); // append search & sorting criteria
    exit;
}

// message from update or cancel on last action
$message = get_session("manager_message");
$message_err = get_session("manager_message_err");
unset($_SESSION["manager_message"]); // reset for next message
unset($_SESSION["manager_message_err"]); // reset for next message

// repopulate page with input
$firstName = get("first-name", $_GET);
$lastName = get("last-name", $_GET);
$product = get("product", $_GET);
$status = get("status", $_GET);
$sort = get("sort", $_GET, "date_desc");

$conn = db_connect();
$orders = false;
$products = []; // if db errs we can still render the page with nothing on it
if ($conn) {
    $orders = search_orders($conn, $firstName, $lastName, $product, $status, $sort);
    $products = get_product_names($conn);
    mysqli_close($conn);
}

// remembered so the redirect after an update or cancel gets on the same results
$_SESSION["manager_search"] = http_build_query([
    "first-name" => $firstName,
    "last-name" => $lastName,
    "product" => $product,
    "status" => $status,
    "sort" => $sort
]);
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
    <link rel="stylesheet" href="styles/style-manager.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <title>Manager</title>
</head>

<body>
    <?php include_once("components/navbar.inc.php") ?>

    <main>
        <br>
        <h1>Order Manager</h1>

        <!-- BEGIN Search -->
        <!-- GET so search stays in address bar so it can be shared -->
        <form id="manager-search" class="manager-search" action="manager.php" method="get">
            <div class="form-col">
                <div>
                    <label for="first-name">First name</label>
                    <span class="field">
                        <input type="text" id="first-name" name="first-name" value="<?php echo $firstName ?>">
                    </span>
                </div>
                <div>
                    <label for="last-name">Last name</label>
                    <span class="field">
                        <input type="text" id="last-name" name="last-name" value="<?php echo $lastName ?>">
                    </span>
                </div>
                <div>
                    <label for="product">Product</label>
                    <select id="product" name="product">
                        <option value="">Any product</option>
                        <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product ?>" <?php echo get_selected("product", $_GET, $product) ?>>
                            <?php echo product_name($product) ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">Any status</option>
                        <?php foreach ($STATUSES as $s) { ?>
                        <option value="<?php echo $s ?>" <?php echo get_selected("status", $_GET, $s) ?>><?php echo ucfirst(strtolower($s)) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="sort">Sort by</label>
                    <select id="sort" name="sort">
                        <?php foreach ($SORTS as $value => $text) { ?>
                        <option value="<?php echo $value ?>" <?php echo $sort === $value ? "selected" : "" ?>><?php echo $text ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="button-row">
                    <button type="submit" class="button-link">Search</button>
                    <a class="button-link button-secondary" href="manager.php">Show all orders</a>
                </div>
            </div>
        </form>
        <!-- END Search -->

        <?php if ($message !== "") { ?>
            <?php if($message_err !== "") { ?>
                <!-- got an error message -->
                <p class="error-box"><?php echo $message ?></p>
            <?php } else { ?>
                <!-- normal message -->
                <p class="manager-message"><?php echo $message ?></p>
            <?php } ?>
        <?php } ?>

        <?php if ($orders === false) { ?>
            <p class="error-box">Unable to load orders.</p>
        <?php } else if (count($orders) === 0) { ?>
            <p>No orders match your search.</p>
        <?php } else { ?>
        <!-- BEGIN Orders -->
        <p><?php echo count($orders) ?> order<?php echo count($orders) === 1 ? "" : "s" // plurality ?> found.</p>
        <!-- wide tables scroll sideways if too small -->
        <div class="table-scroll">
            <table class="cart-table manager-table">
                <thead>
                    <tr>
                        <th scope="col">Order</th>
                        <th scope="col">Date &amp; time</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Products</th>
                        <th scope="col">Total</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) { ?>
                    <tr>
                        <td><?php echo $order["order_id"] ?></td>
                        <td><?php echo date("j M Y, g:i a", strtotime($order["order_time"])) ?></td>
                        <td><?php echo $order["first_name"] . " " . $order["last_name"] ?></td>
                        <td>
                            <ul class="manager-products">
                                <!-- name, size, quantity and unit price on one line per product,
                                see https://www.php.net/manual/en/ref.strings.php for str manip info -->
                                <?php foreach ($order["items"] as $item) { ?>
                                <li><?php echo product_name($item["product"]) . " " . strtoupper($item["capacity"]) . " &times; " . $item["quantity"] . " @ $" . $item["price"] ?></li>
                                <?php } ?>
                                <?php foreach ($order["extras"] as $extra) { ?>
                                <li><?php echo ucfirst(str_replace("-", " ", $extra["extra_name"])) . " ($" . $extra["price"] . ")" ?></li>
                                <?php } ?>
                            </ul>
                        </td>
                        <td>$<?php echo $order["order_cost"] ?></td>
                        <td><?php echo ucfirst(strtolower($order["order_status"])) ?></td>
                        <td>
                            <!-- each update order form in a table row -->
                            <form class="manager-action" action="manager.php" method="post">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="order-id" value="<?php echo $order["order_id"] ?>">
                                <!-- dummy label to stop warnings complaining -->
                                <label for="status-<?php echo $order["order_id"] ?>" hidden>
                                    Order <?php echo $order["order_id"] ?> status
                                </label>
                                <select id="status-<?php echo $order["order_id"] ?>" name="status">
                                    <?php foreach ($STATUSES as $status) { ?>
                                    <option value="<?php echo $status ?>" <?php echo $order["order_status"] === $status ? "selected" : "" ?>><?php echo ucfirst(strtolower($status)) ?></option>
                                    <?php } ?>
                                </select>
                                <label for="submit-<?php echo $order["order_id"] ?>" hidden>
                                    submit order <?php echo $order["order_id"] ?>
                                </label>
                                <button id="submit-<?php echo $order["order_id"] ?>" type="submit" class="button-link">Update</button>
                            </form>

                            <!-- only allow cancelling order if the product is pending ie not shipped yet -->
                            <?php if ($order["order_status"] === "PENDING") { ?>
                            <form class="manager-action" action="manager.php" method="post">
                                <input type="hidden" name="action" value="cancel">
                                <!-- carries order ID to backend logic to update DB -->
                                <input type="hidden" name="order-id" value="<?php echo $order["order_id"] ?>">
                                <label for="cancel-<?php echo $order["order_id"] ?>" hidden>
                                    Cancel order <?php echo $order["order_id"] ?>
                                </label>
                                <button id="cancel-<?php echo $order["order_id"] ?>" type="submit" class="button-link button-secondary">Cancel order</button>
                            </form>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- END Orders -->
        <?php } ?>
    </main>

    <?php include_once("components/footer.inc.php") ?>
</body>

</html>
