<?php

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
