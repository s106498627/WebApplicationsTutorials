<?php
// Description: Various helper fns to be used throughout the project
// Author: Charlie Hutcheson
// Date: 2026-11-03

// php 5 warns on every date function if the server has no date.timezone set, and that warning
// gets printed before header() so redirects break
date_default_timezone_set("Australia/Melbourne");

$PRODUCT_NAMES = [ // to convert db codes to displayable test
    "pokemon-usb" => "Pokemon USB Drive",
    "extreme-usb" => "Extreme Encrypted USB Drive",
    "classic-usb-a" => "Classic USB-A Drive"
];

// remove whitespace and escape html to avoid xss
function sanitise_input($data) {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}

// get a sanitized val from a map
function get($name, $from, $default = "") {
    if (!isset($from[$name]) || !is_string($from[$name])) {
        return $default;
    }
    return sanitise_input($from[$name]);
}

// helper fn to get $_POST vars
function get_post($name, $default = "") {
    return get($name, $_POST, $default);
}

// helper fn to get $_SESSION vars
function get_session($name, $default = "") {
    return get($name, $_SESSION, $default);
}

// helper fn to find if value was selected when posted, so that the new page can keep selected
function get_selected($name, $from, $value) {
    return get($name, $from) === $value ? "selected" : "";
}

// same as get_selected but for radios and checkboxes. checkbox groups like extras[] post an array
function get_checked($name, $from, $value, $default = "") {
    if (isset($from[$name]) && is_array($from[$name])) {
        return in_array($value, $from[$name], true) ? "checked" : ""; // get value is selected from multiple 
    }
    return get($name, $from, $default) === $value ? "checked" : ""; // 1 value only
}

// renders the error box if any errors in the form are encountered, ripped from part2.js
function create_err_box($errMsgs) {
    if (count($errMsgs) === 0) {
        return '<div id="error-box" class="error-box" hidden></div>';
    }

    // construct list of errs
    $html = '<div id="error-box" class="error-box"><p>Fix the following:</p><ul>';
    foreach ($errMsgs as $msg) {
        $html .= "<li>" . $msg . "</li>";
    }
    $html .= "</ul></div>";
    return $html;
}

// gets the posted item count, used in enquire and process order.
function get_item_count() {
    // find number of elements from the number of items posted
    $itemCount = 0; // sentinel 0 is error case
    while (isset($_POST["item-" . ($itemCount + 1) . "-quantity"])) {
        $itemCount++;
    }
    return $itemCount;
}

// get the cheapest price of a major product SKU
function from_price($prices, $product) {
    if (!isset($prices[$product]) || count($prices[$product]) === 0) {
        return "";
    }
    return "From $" . number_format(min($prices[$product]), 2);
}

// get extras price and format for display
function extra_price($extraPrices, $extra) {
    if (!isset($extraPrices[$extra])) {
        return "";
    }
    return " ($" . number_format($extraPrices[$extra], 2) . ")";
}

// gen the attribute for the price of the SKU of the product to give to cart js
function sku_price_attrs($prices, $capacity) {
    $attrs = "";
    foreach ($prices as $product => $capacities) {
        if (isset($capacities[$capacity])) {
            $attrs .= ' data-price-' . $product . '="' . $capacities[$capacity] . '"';
        }
    }
    return $attrs;
}

// get display name for product, or just show the DB name jsut to show anything and not error
function product_name($product) {
    global $PRODUCT_NAMES; // functions can't see globals unless they're pulled in
    return isset($PRODUCT_NAMES[$product]) ? $PRODUCT_NAMES[$product] : $product;
}
