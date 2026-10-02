"use strict";

// TODO move these consts to DB when introduced
const PRODUCT_NAMES = {
    "pokemon-usb": "Pokémon USB Drive",
    "extreme-usb": "Extreme Encrypted USB Drive",
    "classic-usb-a": "Classic USB-A Drive"
};

const PRODUCT_PRICES = {
    "pokemon-usb": 29.95,
    "extreme-usb": 89.95,
    "classic-usb-a": 14.95
};

const STORAGE_NAMES = {
    "32gb": "32 GB",
    "64gb": "64 GB",
    "128gb": "128 GB",
    "256gb": "256 GB"
};
const STORAGE_PRICES = {
    "32gb": 0,
    "64gb": 10,
    "128gb": 20,
    "256gb": 35
};

// optional extras, charged once per order
const EXTRA_NAMES = {
    "express-shipping": "Express shipping",
    "gift-wrap": "Gift wrapping",
    "extended-warranty": "Extended 3-year warranty"
};
const EXTRA_PRICES = {
    "express-shipping": 9.95,
    "gift-wrap": 4.00,
    "extended-warranty": 12.00
};

// valid first digits for state postcodes
const STATE_VALID_DIGITS = {
    VIC: ["3", "8"],
    NSW: ["1", "2"],
    QLD: ["4", "9"],
    ACT: ["0"],
    NT: ["0"],
    WA: ["6"],
    SA: ["5"],
    TAS: ["7"]
}

/**
 * if any errs exist, show the err box of things to fix in the form
 * @param {string} errorId id of the error box
 * @param {string[]} errMsgs list of error messages to construct
 * @returns {boolean} true if no errors
 */
function createErrBox(errorId, errMsgs) {
    // show errs if any
    let errorBox = document.getElementById(errorId);
    if (errMsgs.length > 0) {
        // construct list of errs
        errorBox.innerHTML = "<p>Fix the following:</p><ul>";
        for (const msg of errMsgs) {
            errorBox.innerHTML += "<li>" + msg + "</li>";
        }
        errorBox.innerHTML += "</ul>";
        errorBox.hidden = false;
        return false;
    }

    errorBox.hidden = true;
    return true;
}


/**
 * Validates a postcode for the given state
 * @param {string} state 
 * @param {string} postcode 
 * @returns returns true if the first digit of the postcode is right for the state
 */ 
function isValidPostcode(state, postcode) {
    let first = postcode.charAt(0);
    return STATE_VALID_DIGITS[state].includes(first);
}

function validateDetails(errMsgs) {
    let firstName = document.getElementById("first-name").value.trim();
    let lastName = document.getElementById("last-name").value.trim();
    let email = document.getElementById("email").value.trim();
    let street = document.getElementById("street-address").value.trim();
    let suburb = document.getElementById("suburb").value.trim();
    let state = document.getElementById("state").value;
    let postcode = document.getElementById("postcode").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let contact = document.querySelector('input[name="preferred-contact"]:checked');

    if (firstName === "") {
        errMsgs.push("Enter your first name.");
    } else if (!firstName.match(/^[A-Za-z]{1,25}$/)) {
        errMsgs.push("First name must be letters only, up to 25 characters.");
    }

    if (lastName === "") {
        errMsgs.push("Enter your last name.");
    } else if (!lastName.match(/^[A-Za-z]{1,25}$/)) {
        errMsgs.push("Last name must be letters only, up to 25 characters.");
    }

    if (email === "") {
        errMsgs.push("Enter your email address.");
    } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        // email regex: https://www.mailercheck.com/articles/email-validation-javascript
        errMsgs.push("Enter a valid email address. Example: name@example.com.");
    }

    if (street === "") {
        errMsgs.push("Enter your street address.");
    } else if (!street.match(/^[A-Za-z0-9 ]{1,40}$/)) {
        errMsgs.push("Street address must be letters, numbers and spaces only, up to 40 characters.");
    }

    if (suburb === "") {
        errMsgs.push("Enter your suburb.");
    } else if (!suburb.match(/^[A-Za-z0-9 ]{1,20}$/)) {
        errMsgs.push("Suburb must be letters, numbers and spaces only, up to 20 characters.");
    }

    if (state === "") {
        errMsgs.push("Choose your state.");
    }

    if (postcode === "") {
        errMsgs.push("Enter your postcode.");
    } else if (!postcode.match(/^[0-9]{4}$/)) {
        errMsgs.push("Postcode must be 4 digits.");
    } else if (state !== "" && !isValidPostcode(state, postcode)) {
        errMsgs.push("Postcode does not match the state " + state + ".");
    }

    if (phone === "") {
        errMsgs.push("Enter your phone number.");
    } else if (!phone.match(/^[0-9]{10}$/)) {
        errMsgs.push("Phone number must be 10 digits.");
    }

    if (contact === null) {
        errMsgs.push("Choose a preferred contact method.");
    }
}

function validateCart(errMsgs) {
    let itemCount = document.getElementsByClassName("cart-item").length;

    if (itemCount === 0) {
        errMsgs.push("Add at least one item to cart.");
    }

    for (let i = 1; i <= itemCount; i++) {
        // product name
        let product = document.querySelector('input[name="item-' + i + '-product"]:checked');
        let capacity = document.querySelector('select[name="item-' + i + '-capacity"]').value;
        let quantity = document.querySelector('input[name="item-' + i + '-quantity"]').value.trim();

        // add error msg if no selection are made
        if (product === null) {
            errMsgs.push("Item " + i + ": choose a product.");
        }
        if (capacity === "") {
            errMsgs.push("Item " + i + ": choose a storage size.");
        }
        if (!quantity.match(/^[0-9]+$/) || Number(quantity) < 1) {
            errMsgs.push("Item " + i + ": quantity must be an integer of 1 digit or more.");
        }
    }

    return itemCount;
}

// validate the details and cart so it's good for payment page
function validateEnquire() {
    let errMsgs = [];

    validateDetails(errMsgs);

    // count needed to post to server
    let itemCount = validateCart(errMsgs);

    // show errs, if any stop redirection
    if (!createErrBox("enquire-errors", errMsgs)) {
        // got errors dont go to payment page
        return false;
    }

    // save to local storage for payment page to read
    storeOrder(itemCount);

    // go to payment.html
    window.location.href = "payment.html";
    return false;
}

/** saves order in localStorage so it can be loaded in payment page
 *
 * @param {number} itemCount
 */
function storeOrder(itemCount) {
    localStorage.setItem("firstName", document.getElementById("first-name").value.trim());
    localStorage.setItem("lastName", document.getElementById("last-name").value.trim());
    localStorage.setItem("email", document.getElementById("email").value.trim());
    localStorage.setItem("street", document.getElementById("street-address").value.trim());
    localStorage.setItem("suburb", document.getElementById("suburb").value.trim());
    localStorage.setItem("state", document.getElementById("state").value);
    localStorage.setItem("postcode", document.getElementById("postcode").value.trim());
    localStorage.setItem("phone", document.getElementById("phone").value.trim());
    localStorage.setItem("contact", document.querySelector('input[name="preferred-contact"]:checked').value);
    localStorage.setItem("comment", document.getElementById("comment").value.trim());

    // each cart item is saved as item1Product/Capacity/Quantity item2Product/Capacity/Quantity so on
    localStorage.setItem("itemCount", itemCount);
    for (let i = 1; i <= itemCount; i++) {
        let product = document.querySelector('input[name="item-' + i + '-product"]:checked').value; // selected from frontend
        let storage = document.querySelector('select[name="item-' + i + '-capacity"]').value;
        let quantity = document.querySelector('input[name="item-' + i + '-quantity"]').value.trim();
        localStorage.setItem("item" + i + "Product", product);
        localStorage.setItem("item" + i + "Storage", storage);
        localStorage.setItem("item" + i + "Quantity", quantity);
    }

    // the ticked extras are saved as one string "express-shipping,gift-wrap"
    // TODO: maybe change to JSON extra true/false
    let extras = "";
    let extraBoxes = document.getElementsByName("extras");
    for (let i = 0; i < extraBoxes.length; i++) {
        if (extraBoxes[i].checked) {
            if (extras !== "") {
                extras += ",";
            }
            extras += extraBoxes[i].value;
        }
    }
    localStorage.setItem("extras", extras);
}


// fills in the order summary and the hidden inputs from localStorage
function showOrder() {
    // no order saved, so there is nothing to pay for
    if (localStorage.getItem("firstName") == null) {
        document.getElementById("no-order").hidden = false;
        document.getElementById("order-summary").hidden = true;
        document.getElementById("payment-form").hidden = true;
        return;
    }

    let firstName = localStorage.getItem("firstName");
    let lastName = localStorage.getItem("lastName");
    let email = localStorage.getItem("email");
    let street = localStorage.getItem("street");
    let suburb = localStorage.getItem("suburb");
    let state = localStorage.getItem("state");
    let postcode = localStorage.getItem("postcode");
    let phone = localStorage.getItem("phone");
    let contact = localStorage.getItem("contact");
    let comment = localStorage.getItem("comment");

    // customer details
    document.getElementById("confirm-name").textContent = firstName + " " + lastName;
    document.getElementById("confirm-email").textContent = email;
    document.getElementById("confirm-phone").textContent = phone;
    document.getElementById("confirm-address").textContent = street + ", " + suburb + ", " + state + ", " + postcode;
    document.getElementById("confirm-contact").textContent = contact;
    document.getElementById("confirm-comment").textContent = comment;

    // one table row and three hidden inputs for each cart item
    let itemCount = Number(localStorage.getItem("itemCount"));
    let total = 0;
    let rows = "";
    let hiddenInputs = "";

    for (let i = 1; i <= itemCount; i++) {
        let product = localStorage.getItem("item" + i + "Product");
        let storage = localStorage.getItem("item" + i + "Storage");
        let quantity = Number(localStorage.getItem("item" + i + "Quantity"));

        // order calcs
        let unitPrice = PRODUCT_PRICES[product] + STORAGE_PRICES[storage];
        let lineTotal = unitPrice * quantity;
        total = total + lineTotal;

        // table for display to user to see what they selected
        rows += "<tr>";
        rows += "<td>" + PRODUCT_NAMES[product] + "</td>";
        rows += "<td>" + STORAGE_NAMES[storage] + "</td>";
        rows += "<td>$" + unitPrice.toFixed(2) + "</td>";
        rows += "<td>" + quantity + "</td>";
        rows += "<td>$" + lineTotal.toFixed(2) + "</td>";
        rows += "</tr>";

        // added to form invisibly to send iwth card details to server
        hiddenInputs += '<input type="hidden" name="item-' + i + '-product" value="' + product + '">';
        hiddenInputs += '<input type="hidden" name="item-' + i + '-capacity" value="' + storage + '">';
        hiddenInputs += '<input type="hidden" name="item-' + i + '-quantity" value="' + quantity + '">';
    }
    document.getElementById("cart-rows").innerHTML = rows;
    document.getElementById("h-cart-items").innerHTML = hiddenInputs;

    // extract extras from storage
    let extras = localStorage.getItem("extras");
    let extrasText = "None"; // default for no extras
    if (extras !== "") {
        let extraList = extras.split(","); // turn string back into array
        let extrasCost = 0;

        extrasText = "";
        for (let i = 0; i < extraList.length; i++) {
            extrasCost = extrasCost + EXTRA_PRICES[extraList[i]];
            if (extrasText !== "") {
                extrasText += ", ";
            }
            extrasText += EXTRA_NAMES[extraList[i]];
        }
        extrasText += " ($" + extrasCost.toFixed(2) + ")";
        total = total + extrasCost;
    }

    // round total to 2dp - toFixed might truncate 
    total = Math.round(total * 100) / 100;

    document.getElementById("confirm-extras").textContent = extrasText;
    document.getElementById("confirm-total").textContent = "$" + total.toFixed(2);

    // copy everything into the hidden inputs so it is sent to the server with the card details
    document.getElementById("h-first-name").value = firstName;
    document.getElementById("h-last-name").value = lastName;
    document.getElementById("h-email").value = email;
    document.getElementById("h-street-address").value = street;
    document.getElementById("h-suburb").value = suburb;
    document.getElementById("h-state").value = state;
    document.getElementById("h-postcode").value = postcode;
    document.getElementById("h-phone").value = phone;
    document.getElementById("h-preferred-contact").value = contact;
    document.getElementById("h-comment").value = comment;
    document.getElementById("h-item-count").value = itemCount; // server needs this to know how many items to get
    document.getElementById("h-extras").value = extras;
    document.getElementById("h-total-cost").value = total.toFixed(2);
}

// validate card info and send details and card info on
function validatePayment() {
    let errMsgs = [];

    let cardType = document.getElementById("card-type").value;
    let cardName = document.getElementById("card-name").value.trim();
    let cardNumber = document.getElementById("card-number").value.trim();
    let expiry = document.getElementById("card-expiry").value.trim();
    let cvv = document.getElementById("card-cvv").value.trim();

    if (cardType === "") {
        errMsgs.push("Choose a card type.");
    }

    if (cardName === "") {
        errMsgs.push("Enter the name on the card.");
    } else if (!cardName.match(/^[A-Za-z ]{1,40}$/)) {
        errMsgs.push("<Name on card must be letters and spaces only, up to 40 characters.");
    }

    // number must be 15 or 16 digits and must suit card type
    if (cardNumber === "") {
        errMsgs.push("Enter the card number.");
    } else if (!cardNumber.match(/^[0-9]{15,16}$/)) {
        errMsgs.push("Card number must be 15 or 16 digits.");
    } else if (cardType === "visa" && !cardNumber.match(/^4[0-9]{15}$/)) {
        errMsgs.push("Visa card numbers must be 16 digits and start with 4.");
    } else if (cardType === "mastercard" && !cardNumber.match(/^5[1-5][0-9]{14}$/)) {
        errMsgs.push("Mastercard numbers must be 16 digits and start with 51 to 55.");
    } else if (cardType === "americanExpress" && !cardNumber.match(/^3[47][0-9]{13}$/)) {
        errMsgs.push("American Express numbers must be 15 digits and start with 34 or 37.");
    }

    if (expiry === "") {
        errMsgs.push("Enter the expiry date.");
    } else if (!expiry.match(/^(0[1-9]|1[0-2])-[0-9]{2}$/)) {
        errMsgs.push("Expiry date must be in the format MM-YY.");
    }

    if (cvv === "") {
        errMsgs.push("Enter the CVV.");
    } else if (!cvv.match(/^[0-9]{3}$/)) {
        errMsgs.push("CVV must be exactly 3 digits.");
    }

    //show errs if any
    return createErrBox("payment-errors", errMsgs);
}

// delete order from localStorage and redirect to home
function cancelOrder() {
    localStorage.clear();
    window.location.href = "index.html"; // redirectto homepage
}

function init() {

    // implies on payment form if `payment-form` id is on page, same for `enquire-form`
    let enquireForm = document.getElementById("enquire-form");
    let paymentForm = document.getElementById("payment-form");

    if (enquireForm != null) {
        enquireForm.onsubmit = validateEnquire;
    }

    if (paymentForm != null) {
        showOrder();
        paymentForm.onsubmit = validatePayment;
        document.getElementById("cancel-order").onclick = cancelOrder;
    }

    // set up the cart
    initEnhancements();
}

window.onload = init;
