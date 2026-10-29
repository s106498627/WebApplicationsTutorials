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

// validation is done by payment.php now, so just save the order for the payment page
// and let the form post. php sends you back to enquire.php if it is invalid
function validateEnquire() {
    storeOrder(document.getElementsByClassName("cart-item").length);
    return true;
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
    // nothing may be ticked yet as php validates after this, so save "" instead of erroring
    let contact = document.querySelector('input[name="preferred-contact"]:checked');
    localStorage.setItem("contact", contact ? contact.value : "");
    localStorage.setItem("comment", document.getElementById("comment").value.trim());

    // each cart item is saved as item1Product/Capacity/Quantity item2Product/Capacity/Quantity so on
    localStorage.setItem("itemCount", itemCount);
    for (let i = 1; i <= itemCount; i++) {
        let checked = document.querySelector('input[name="item-' + i + '-product"]:checked'); // selected from frontend
        let product = checked ? checked.value : "";
        let storage = document.querySelector('select[name="item-' + i + '-capacity"]').value;
        let quantity = document.querySelector('input[name="item-' + i + '-quantity"]').value.trim();
        localStorage.setItem("item" + i + "Product", product);
        localStorage.setItem("item" + i + "Storage", storage);
        localStorage.setItem("item" + i + "Quantity", quantity);
    }

    // the ticked extras are saved as one string "express-shipping,gift-wrap"
    // TODO: maybe change to JSON extra true/false
    let extras = "";
    let extraBoxes = document.getElementsByName("extras[]");
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
    document.getElementById("h-extras").value = extras;
}

// delete order from localStorage and redirect to home
function cancelOrder() {
    localStorage.clear();
    window.location.href = "index.php"; // redirect to homepage
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
        document.getElementById("cancel-order").onclick = cancelOrder;
    }

    // set up the cart
    initEnhancements();
}

window.onload = init;
