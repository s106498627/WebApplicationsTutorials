<?php
// Description: All the validations (cart, details and payments) for site
// Author: Charlie Hutcheson
// Date: 2026-11-03

require_once("helpers.php");

// valid first digits for state postcodes
$STATE_VALID_DIGITS = [
    "VIC" => ["3", "8"],
    "NSW" => ["1", "2"],
    "QLD" => ["4", "9"],
    "ACT" => ["0"],
    "NT" => ["0"],
    "WA" => ["6"],
    "SA" => ["5"],
    "TAS" => ["7"]
];

// returns true if the postcode is valid
function is_valid_postcode($state, $postcode) {
    global $STATE_VALID_DIGITS;
    // js would throw on an unknown state, here it just fails the check
    if (!isset($STATE_VALID_DIGITS[$state])) {
        return false;
    }
    $first = substr($postcode, 0, 1);
    return in_array($first, $STATE_VALID_DIGITS[$state]); //equ to contains() in other langs
}

function validate_details(&$errMsgs) {
    $firstName = get_post("first-name");
    $lastName = get_post("last-name");
    $email = get_post("email");
    $street = get_post("street-address");
    $suburb = get_post("suburb");
    $state = get_post("state");
    $postcode = get_post("postcode");
    $phone = get_post("phone");
    $contact = get_post("preferred-contact");

    // copy-pasta str8 from js
    if ($firstName === "") {
        $errMsgs[] = "Enter your first name.";
    } else if (!preg_match('/^[A-Za-z]{1,25}$/', $firstName)) {
        $errMsgs[] = "First name must be letters only, up to 25 characters.";
    }

    if ($lastName === "") {
        $errMsgs[] = "Enter your last name.";
    } else if (!preg_match('/^[A-Za-z]{1,25}$/', $lastName)) {
        $errMsgs[] = "Last name must be letters only, up to 25 characters.";
    }

    if ($email === "") {
        $errMsgs[] = "Enter your email address.";
    } else if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
        // email regex: https://www.mailercheck.com/articles/email-validation-javascript
        $errMsgs[] = "Enter a valid email address. Example: name@example.com.";
    }

    if ($street === "") {
        $errMsgs[] = "Enter your street address.";
    } else if (!preg_match('/^[A-Za-z0-9 ]{1,40}$/', $street)) {
        $errMsgs[] = "Street address must be letters, numbers and spaces only, up to 40 characters.";
    }

    if ($suburb === "") {
        $errMsgs[] = "Enter your suburb.";
    } else if (!preg_match('/^[A-Za-z0-9 ]{1,20}$/', $suburb)) {
        $errMsgs[] = "Suburb must be letters, numbers and spaces only, up to 20 characters.";
    }

    if ($state === "") {
        $errMsgs[] = "Choose your state.";
    }

    if ($postcode === "") {
        $errMsgs[] = "Enter your postcode.";
    } else if (!preg_match('/^[0-9]{4}$/', $postcode)) {
        $errMsgs[] = "Postcode must be 4 digits.";
    } else if ($state !== "" && !is_valid_postcode($state, $postcode)) {
        $errMsgs[] = "Postcode does not match the state " . $state . ".";
    }

    if ($phone === "") {
        $errMsgs[] = "Enter your phone number.";
    } else if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errMsgs[] = "Phone number must be 10 digits.";
    }

    if ($contact === "") {
        $errMsgs[] = "Choose a preferred contact method.";
    }
}

function validate_cart(&$errMsgs) {
    // ripped from part2.js
    
    // find number of elements from the number of items posted
    $itemCount = get_item_count();

    if ($itemCount === 0) {
        $errMsgs[] = "Add at least one item to cart.";
    }

    for ($i = 1; $i <= $itemCount; $i++) {
        $product = get_post("item-" . $i . "-product");
        $capacity = get_post("item-" . $i . "-capacity");
        $quantity = get_post("item-" . $i . "-quantity");

        // add error msg if no selection are made
        if ($product === "") {
            $errMsgs[] = "Item " . $i . ": choose a product.";
        }
        if ($capacity === "") {
            $errMsgs[] = "Item " . $i . ": choose a storage size.";
        }
        if (!preg_match('/^[0-9]+$/', $quantity) || (int)$quantity < 1) {
            $errMsgs[] = "Item " . $i . ": quantity must be an integer of 1 digit or more.";
        }
    }

    return $itemCount;
}

// validate the details and cart so it's good for payment page
function validate_enquire(&$errMsgs) {
    validate_details($errMsgs);
    validate_cart($errMsgs);
}

// validate card info
function validate_payment(&$errMsgs) {

    $cardType = get_post("card-type");
    $cardName = get_post("card-name");
    $cardNumber = get_post("card-number");
    $expiry = get_post("card-expiry");
    $cvv = get_post("card-cvv");

    if ($cardType === "") {
        $errMsgs[] = "Choose a card type.";
    }

    if ($cardName === "") {
        $errMsgs[] = "Enter the name on the card.";
    } else if (!preg_match('/^[A-Za-z ]{1,40}$/', $cardName)) {
        $errMsgs[] = "Name on card must be letters and spaces only, up to 40 characters.";
    }

    // number must be 15 or 16 digits and must suit card type
    if ($cardNumber === "") {
        $errMsgs[] = "Enter the card number.";
    } else if (!preg_match('/^[0-9]{15,16}$/', $cardNumber)) {
        $errMsgs[] = "Card number must be 15 or 16 digits.";
    } else if ($cardType === "visa" && !preg_match('/^4[0-9]{15}$/', $cardNumber)) {
        $errMsgs[] = "Visa card numbers must be 16 digits and start with 4.";
    } else if ($cardType === "mastercard" && !preg_match('/^5[1-5][0-9]{14}$/', $cardNumber)) {
        $errMsgs[] = "Mastercard numbers must be 16 digits and start with 51 to 55.";
    } else if ($cardType === "americanExpress" && !preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
        $errMsgs[] = "American Express numbers must be 15 digits and start with 34 or 37.";
    }

    if ($expiry === "") {
        $errMsgs[] = "Enter the expiry date.";
    } else if (!preg_match('/^(0[1-9]|1[0-2])-[0-9]{2}$/', $expiry)) {
        $errMsgs[] = "Expiry date must be in the format MM-YY.";
    } else { // check expiry date to see if out of date
        $mmyy = date_create_from_format("m-y", $expiry);
        date_modify($mmyy, "+1 month"); // card valid until the last day of the month: https://www.reddit.com/r/CreditCards/comments/omeb89/are_credit_cards_good_until_the_last_day_of_the/
        $now = date_create();
        if ($mmyy < $now) {
            $errMsgs[] = "Card is expired.";
        }
    }

    if ($cvv === "") {
        $errMsgs[] = "Enter the CVV.";
    } else if (!preg_match('/^[0-9]{3}$/', $cvv)) {
        $errMsgs[] = "CVV must be exactly 3 digits.";
    }
}
