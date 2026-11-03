<?php
function db_connect() {
    require("settings.php"); // include here to limit scope vars are in.
    // @ hides warnings
    $conn = @mysqli_connect($host, $user, $pwd, $sql_db);
    return $conn;
}

// takes the customer information and inserts it if needed, returns customer id 
function save_customer($conn, $customer) {
    // escape everything before it goes in the query to prevent injection
    $firstName = mysqli_real_escape_string($conn, $customer["first_name"]);
    $lastName = mysqli_real_escape_string($conn, $customer["last_name"]);
    $email = mysqli_real_escape_string($conn, $customer["email"]);
    $street = mysqli_real_escape_string($conn, $customer["street_address"]);
    $suburb = mysqli_real_escape_string($conn, $customer["suburb"]);
    $state = mysqli_real_escape_string($conn, $customer["state"]);
    $postcode = mysqli_real_escape_string($conn, $customer["postcode"]);
    $phone = mysqli_real_escape_string($conn, $customer["phone"]);

    // in form its "email" but enum in db needs uppercase
    $contact = mysqli_real_escape_string($conn, strtoupper($customer["preferred_contact"]));

    // get id to update if needed
    $query = "SELECT customer_id FROM customers WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        return false; // query failec toexec somehow?
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    if ($row) {    
        // email uniquely identifies customer, 
        // update any other details if changed when entering a second time
        $customerId = $row["customer_id"];
        $query = "UPDATE customers SET first_name = '$firstName', last_name = '$lastName',
            street_address = '$street', suburb = '$suburb', state = '$state', postcode = '$postcode',
            phone = '$phone', preferred_contact = '$contact' WHERE customer_id = '$customerId'";
        if (!mysqli_query($conn, $query)) {
            return false; // query failed to exec
        }
    } else {
        // create new customer
        $query = "INSERT INTO customers (first_name, last_name, email, street_address, suburb, state,
            postcode, phone, preferred_contact) VALUES ('$firstName', '$lastName', '$email', '$street',
            '$suburb', '$state', '$postcode', '$phone', '$contact')";
        if (!mysqli_query($conn, $query)) {
            return false;// failed to exec
        }
        // get the id returned from the increment to populate the order with
        $customerId = mysqli_insert_id($conn);
    }

    return $customerId;
}

// get id and price of the product and capacity combination, returns false if none is found
function get_product($conn, $product, $capacity) {
    $product = mysqli_real_escape_string($conn, $product);
    $capacity = mysqli_real_escape_string($conn, $capacity);

    $result = mysqli_query($conn, "SELECT product_id, price FROM products
        WHERE product = '$product' AND capacity = '$capacity'");
    if (!$result) {
        return false;
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    if (!$row) {
        return false;
    }
    return $row;
}

// get id and price of the extra, returns false if none is found
function get_extra($conn, $extraName) {
    $extraName = mysqli_real_escape_string($conn, $extraName);

    $result = mysqli_query($conn, "SELECT extra_id, price FROM extras WHERE extra_name = '$extraName'");
    if (!$result) {
        return false;
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    if (!$row) {
        return false;
    }
    return $row;
}

// inserts the new order, returns order id, or false if error occured
function create_order($conn, $customer, $card, $items, $extras, $comment) {
    $customerId = save_customer($conn, $customer);
    if ($customerId === false) {
        return false; // need to have a customer to make an order
    }

    // find item price and add to total
    $total = 0.00;
    $quantities = []; // product_id = quantity
    foreach ($items as $item) {
        $product = get_product($conn, $item["product"], $item["capacity"]);
        if ($product === false) {
            return false; // couldnt find the product
        } else {
            $productId = $product["product_id"];
            if (!isset($quantities[$productId])) {
                $quantities[$productId] = 0; // first line for this product
            }
            // add rather than set, the same product can be in the cart on more than one line
            $quantities[$productId] += (int)$item["quantity"];
            $total += $product["price"] * (int)$item["quantity"]; // running count on total price
        }
    }

    // now same for extras
    $extraIds = [];
    foreach ($extras as $extraName) {
        $extra = get_extra($conn, $extraName);
        if ($extra === false) {
            return false; // cant find extra
        } else {
            $extraIds[] = $extra["extra_id"];
            $total += $extra["price"];
        }
    }
    $total = round($total, 2);

    // save the order
    $orderId = false;

    $customerId = (int)$customerId;
    $comment = mysqli_real_escape_string($conn, $comment);
    $cardType = mysqli_real_escape_string($conn, $card["card_type"]);
    $cardName = mysqli_real_escape_string($conn, $card["card_name"]);
    $cardNumber = mysqli_real_escape_string($conn, $card["card_number"]);
    $cardExpiry = mysqli_real_escape_string($conn, $card["card_expiry"]);

    $query = "INSERT INTO orders (customer_id, comment, card_type, card_name, card_number, card_expiry,
        order_cost) VALUES ('$customerId', '$comment', '$cardType', '$cardName', '$cardNumber',
        '$cardExpiry', '$total')";
    if (mysqli_query($conn, $query)) {
        $orderId = mysqli_insert_id($conn); // getting the new id from mysql
    } else {
        return false; // failed to insert order
    }
    

    // save each item
    foreach ($quantities as $productId => $quantity) {
        $productId = (int)$productId;
        $quantity = (int)$quantity;
        $query = "INSERT INTO order_items (order_id, product_id, quantity)
            VALUES ('$orderId', '$productId', '$quantity')";
        if (!mysqli_query($conn, $query)) {
            return false; // failed to insert items
        }
    }


    // save each extra
    foreach ($extraIds as $extraId) {
        $extraId = (int)$extraId;
        $query = "INSERT INTO order_extras (order_id, extra_id) VALUES ('$orderId', '$extraId')";
        if (!mysqli_query($conn, $query)) {
            return false; // filed to insert extra
        }
    }

    return $orderId;
}

// get the whole order row from it's orderId including joint info ie items and extras, or false if any errors
function get_order($conn, $orderId) {
    $orderId = (int)$orderId;

    // order and the customer who made it
    $result = mysqli_query($conn, "SELECT o.order_id, o.customer_id, o.comment, o.card_type, o.card_name,
            o.card_number, o.card_expiry, o.order_cost, o.order_time, o.order_status,
            c.first_name, c.last_name, c.email, c.street_address, c.suburb, c.state, c.postcode,
            c.phone, c.preferred_contact
        FROM orders o JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.order_id = '$orderId'");
    if (!$result) {
        return false;
    }
    $order = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    if (!$order) {
        return false;
    }

    $order["items"] = get_order_items($conn, $orderId);
    $order["extras"] = get_order_extras($conn, $orderId);
    if ($order["items"] === false || $order["extras"] === false) {
        return false;
    }

    return $order;
}

// get the products on an order with their quantities, or false if any errors
function get_order_items($conn, $orderId) {
    $orderId = (int)$orderId;

    $result = mysqli_query($conn, "SELECT p.product_id, p.product, p.capacity, p.price, oi.quantity
        FROM order_items oi JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = '$orderId'");
    if (!$result) {
        return false;
    }
    // keep grabbing rows until we run out of hits
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    mysqli_free_result($result);

    return $items;
}

// get the extras chosen on an order, or false if any errors
function get_order_extras($conn, $orderId) {
    $orderId = (int)$orderId;

    $result = mysqli_query($conn, "SELECT e.extra_id, e.extra_name, e.price
        FROM order_extras oe JOIN extras e ON oe.extra_id = e.extra_id
        WHERE oe.order_id = '$orderId'");
    if (!$result) {
        return false;
    }
    $extras = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $extras[] = $row;
    }
    mysqli_free_result($result);

    return $extras;
}

// get the unique product skus that exist, for the manager's product filter
function get_product_names($conn) {
    $result = mysqli_query($conn, "SELECT DISTINCT product FROM products ORDER BY product");
    if (!$result) {
        return false;
    }
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row["product"];
    }
    mysqli_free_result($result);

    return $products;
}

// price of every product and capacity combination for cart $prices[product][capacity] = price
// returns false if any errors
function get_product_prices($conn) {
    $result = mysqli_query($conn, "SELECT product, capacity, price FROM products");
    if (!$result) {
        return false;
    }
    $prices = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $prices[$row["product"]][$row["capacity"]] = $row["price"];
    }
    mysqli_free_result($result);

    return $prices;
}

// price of every extra for the cart, as $prices[extra_name] = price. returns false if any errors
function get_extra_prices($conn) {
    $result = mysqli_query($conn, "SELECT extra_name, price FROM extras");
    if (!$result) {
        return false;
    }
    $prices = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $prices[$row["extra_name"]] = $row["price"];
    }
    mysqli_free_result($result);

    return $prices;
}

// search orders for the manager page. filters are optional, empty string = dont filter.
// $sort is has to be 1 of `date_` or `cost_` + `asc` or `desc`.
function search_orders($conn, $firstName, $lastName, $product, $status, $sort) {
    // whitelist of sorts so the ORDER BY can't be injected
    $ORDER_SORTS = [
        "date_desc" => "o.order_time DESC",
        "date_asc" => "o.order_time ASC",
        "cost_desc" => "o.order_cost DESC",
        "cost_asc" => "o.order_cost ASC"
    ];
    $orderBy = isset($ORDER_SORTS[$sort]) ? $ORDER_SORTS[$sort] : $ORDER_SORTS["date_desc"]; // provide usable default

    // build up the where clause from whichever filters were given
    $where = [];
    if ($firstName !== "") {
        // partial matches
        $firstName = mysqli_real_escape_string($conn, $firstName);
        $where[] = "c.first_name LIKE '%$firstName%'";
    }
    if ($lastName !== "") {
        $lastName = mysqli_real_escape_string($conn, $lastName);
        $where[] = "c.last_name LIKE '%$lastName%'";
    }
    if ($product !== "") {
        // orders that have at least one of that product on them
        $product = mysqli_real_escape_string($conn, $product);
        $where[] = "o.order_id IN (SELECT oi.order_id FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id WHERE p.product = '$product')";
    }
    if ($status !== "") {
        $status = mysqli_real_escape_string($conn, strtoupper($status));
        $where[] = "o.order_status = '$status'";
    }
    // implode == string.join() in more serious langs, i like the whimsy: https://www.php.net/manual/en/function.implode.php
    $whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

    $result = mysqli_query($conn, "SELECT o.order_id, o.order_time, o.order_cost, o.order_status,
            c.first_name, c.last_name
        FROM orders o JOIN customers c ON o.customer_id = c.customer_id
        $whereSql
        ORDER BY $orderBy, o.order_id DESC");
    if (!$result) {
        return false;
    }
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    mysqli_free_result($result);

    // attach what was bought in each order
    foreach ($orders as $i => $order) {
        $orders[$i]["items"] = get_order_items($conn, $order["order_id"]);
        $orders[$i]["extras"] = get_order_extras($conn, $order["order_id"]);
        if ($orders[$i]["items"] === false || $orders[$i]["extras"] === false) {
            return false;
        }
    }

    return $orders;
}

// change status of order by orderId, returns false if status isnt valid or update failed
function update_order_status($conn, $orderId, $status) {
    $orderId = (int)$orderId;
    $status = strtoupper($status);
    // check for valid enum options
    if (!in_array($status, ["PENDING", "FULFILLED", "PAID", "ARCHIVED"], true)) {
        return false;
    }

    return mysqli_query($conn, "UPDATE orders SET order_status = '$status' WHERE order_id = '$orderId'");
}

// remove pending order and rows that reference it
function cancel_order($conn, $orderId) {
    $orderId = (int)$orderId;

    $result = mysqli_query($conn, "SELECT order_status FROM orders WHERE order_id = '$orderId' FOR UPDATE");
    if (!$result) {
        return false;
    }
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    if (!$row || $row["order_status"] !== "PENDING") {
        return false; // only pending orders can be cancelled
    }

    // delete leaves before the trunk
    if (!mysqli_query($conn, "DELETE FROM order_items WHERE order_id = '$orderId'")
        || !mysqli_query($conn, "DELETE FROM order_extras WHERE order_id = '$orderId'")
        || !mysqli_query($conn, "DELETE FROM orders WHERE order_id = '$orderId'")) {
        return false;
    }

    return true;
}
