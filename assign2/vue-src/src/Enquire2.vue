<template>
    <br>
    <h1>Order &amp; Enquiry</h1>
    <!-- POST not GET to not leak form inputs to the address bar. js does the redirect instead. 
     @submit.prevent stops default submit behaviour and instead refers to delegate to handle that -->

    <form id="enquire-form" action="payment.html" method="post" novalidate @submit.prevent="validateEnquire">
        <!-- left col -->
        <div class="form-col">
            <EnquireDetails v-model="details" />
        </div>
        <!-- right col -->
        <div class="form-col">
            <EnquireCart v-model:cart="cart" v-model:extras="extras" v-model:comment="comment" />
            <ErrorBox v-model:errMsgs="errMsgs" />
            <button type="submit" class="button-link">Pay Now</button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { ref } from "vue";
import EnquireCart from "./EnquireCart.vue";
import EnquireDetails from "./EnquireDetails.vue";
import { EXTRA_NAMES, STATE_VALID_DIGITS } from "./consts";
import type { CartItem, Details } from "./types";

// passed into the EnquireDetails component to fill out, and validated here
const details = ref<Details>({
    firstName: "",
    lastName: "",
    email: "",
    street: "",
    suburb: "",
    state: "",
    postcode: "",
    phone: "",
    contact: "email"
});
// passed into EnquireCart to fill out
const cart = ref<CartItem[]>([]);
const extras = ref<string[]>([]);
const comment = ref<string>("");

// accumulate any errs to show in ErrorBox
const errMsgs = ref<string[]>([]);

/**
 * Validates a postcode for the given state
 * @returns true if the first digit of the postcode is right for the state
 */
function isValidPostcode(state: string, postcode: string) {
    let first = postcode.charAt(0);
    return STATE_VALID_DIGITS[state].includes(first);
}

/**
 * Validates the details form, adds any errors to msgs array
 */
function validateDetails(msgs: string[]) {
    let firstName = details.value.firstName.trim();
    let lastName = details.value.lastName.trim();
    let email = details.value.email.trim();
    let street = details.value.street.trim();
    let suburb = details.value.suburb.trim();
    let state = details.value.state;
    let postcode = details.value.postcode.trim();
    let phone = details.value.phone.trim();
    let contact = details.value.contact;

    if (firstName === "") {
        msgs.push("Enter your first name.");
    } else if (!firstName.match(/^[A-Za-z]{1,25}$/)) {
        msgs.push("First name must be letters only, up to 25 characters.");
    }

    if (lastName === "") {
        msgs.push("Enter your last name.");
    } else if (!lastName.match(/^[A-Za-z]{1,25}$/)) {
        msgs.push("Last name must be letters only, up to 25 characters.");
    }

    if (email === "") {
        msgs.push("Enter your email address.");
    } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        // email regex: https://www.mailercheck.com/articles/email-validation-javascript
        msgs.push("Enter a valid email address, e.g. name@example.com.");
    }

    if (street === "") {
        msgs.push("Enter your street address.");
    } else if (!street.match(/^[A-Za-z0-9 ]{1,40}$/)) {
        msgs.push("Street address must be letters, numbers and spaces only, up to 40 characters.");
    }

    if (suburb === "") {
        msgs.push("Enter your suburb.");
    } else if (!suburb.match(/^[A-Za-z0-9 ]{1,20}$/)) {
        msgs.push("Suburb must be letters, numbers and spaces only, up to 20 characters.");
    }

    if (state === "") {
        msgs.push("Choose your state.");
    }

    if (postcode === "") {
        msgs.push("Enter your postcode.");
    } else if (!postcode.match(/^[0-9]{4}$/)) {
        msgs.push("Postcode must be 4 digits.");
    } else if (state !== "" && !isValidPostcode(state, postcode)) {
        msgs.push("Postcode does not match the state " + state + ".");
    }

    if (phone === "") {
        msgs.push("Enter your phone number.");
    } else if (!phone.match(/^[0-9]{10}$/)) {
        msgs.push("Phone number must be 10 digits.");
    }

    if (contact === "") {
        msgs.push("Choose a preferred contact method.");
    }
}

/**
 * Validates the cart form, adds any errors to msgs array
 */
function validateCart(msgs: string[]) {
    if (cart.value.length === 0) {
        msgs.push("Add at least one item to cart.");
    }

    cart.value.forEach((item, index) => {
        let number = index + 1;
        let quantity = item.quantity.trim();

        // add error msg if no selection are made
        if (item.product === "") {
            msgs.push("Item " + number + ": choose a product.");
        }
        if (item.capacity === "") {
            msgs.push("Item " + number + ": choose a storage size.");
        }
        if (!quantity.match(/^[0-9]+$/) || Number(quantity) < 1) {
            msgs.push("Item " + number + ": quantity must be an integer of 1 digit or more.");
        }
    });
}

/** Validate the details and cart so it's good for payment page
 */
function validateEnquire() {
    let msgs: string[] = [];

    validateDetails(msgs);
    validateCart(msgs);

    // show errs, if any stop redirection
    errMsgs.value = msgs; // update the ErrorBox view
    if (msgs.length > 0) {
        return;
    }

    // save to local storage for payment page to read
    storeOrder();

    window.location.href = "payment.html";
}

// saves order in localStorage so it can be loaded in payment page
function storeOrder() {
    localStorage.setItem("firstName", details.value.firstName.trim());
    localStorage.setItem("lastName", details.value.lastName.trim());
    localStorage.setItem("email", details.value.email.trim());
    localStorage.setItem("street", details.value.street.trim());
    localStorage.setItem("suburb", details.value.suburb.trim());
    localStorage.setItem("state", details.value.state);
    localStorage.setItem("postcode", details.value.postcode.trim());
    localStorage.setItem("phone", details.value.phone.trim());
    localStorage.setItem("contact", details.value.contact);
    localStorage.setItem("comment", comment.value.trim());

    // each cart item is saved as item1Product/Storage/Quantity item2Product/Storage/Quantity so on
    localStorage.setItem("itemCount", String(cart.value.length));
    cart.value.forEach((item, index) => {
        let number = index + 1; // no longer have to keep count of items in cart in the HTML, just get idx from array iteration!!
        localStorage.setItem("item" + number + "Product", item.product);
        localStorage.setItem("item" + number + "Storage", item.capacity);
        localStorage.setItem("item" + number + "Quantity", item.quantity.trim()); // trim user input
    });

    // the ticked extras are saved as one string "express-shipping,gift-wrap", in the order shown on the form
    let ticked = Object.keys(EXTRA_NAMES).filter((extra) => extras.value.includes(extra)); // get option names -> filter out unchecked optoins
    localStorage.setItem("extras", ticked.join(","));
}
</script>
