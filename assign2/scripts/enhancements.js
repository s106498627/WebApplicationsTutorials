"use strict";

// numbers cart cards according to position ie item-1-product, item-1-capacity, item-1-quantity -> item-2-* so on
// returns the number of items in the cart
function renumberItems() {
    let items = document.getElementsByClassName("cart-item");

    for (let i = 0; i < items.length; i++) {
        let number = i + 1;

        //  item 'x' heading in cart card
        items[i].getElementsByClassName("cart-item-number")[0].textContent = number;

        // reorder data-field values so they are linear 1 to x again
        let fields = items[i].querySelectorAll("[data-field]");
        for (let j = 0; j < fields.length; j++) {
            fields[j].name = "item-" + number + "-" + fields[j].getAttribute("data-field");
        }

        // hide delete button if there is only 1 item
        let deleteButton = items[i].getElementsByClassName("cart-remove")[0];
        deleteButton.hidden = items.length === 1;
    }

    return items.length;
}

// copies the hidden template to make a new item card
function addItem() {
    let template = document.getElementById("cart-item-template");
    let newItem = template.content.firstElementChild.cloneNode(true);

    // put the new card just before the "Add item" button
    let addButton = document.getElementById("add-item");
    addButton.parentNode.insertBefore(newItem, addButton);

    // set delete onclick delegate
    newItem.getElementsByClassName("cart-remove")[0].onclick = removeItem;
    let numItems = renumberItems();

    if (numItems <= 1) {
        // need to hide the delete button so you HAVE to have 1 item to submit
        newItem.getElementsByClassName("cart-remove")[0].hidden = true;
    } else {
        // make sure the dlete buttons are visible now that there is more than 1 item
        let deleteButtons = document.getElementsByClassName("cart-remove");
        for (let i = 0; i < deleteButtons.length; i++) {
            deleteButtons[i].hidden = false;
        }
    }
}

// removes the card that the clicked bin button is in
function removeItem() {
    // "this" is the button that was clicked
    let item = this.closest(".cart-item");
    item.remove();
    renumberItems();
}

// called by init() in part2.js
function initEnhancements() {
    let addButton = document.getElementById("add-item");

    // only the enquire page has a cart
    if (addButton == null) {
        return;
    }

    addButton.onclick = addItem;

    // pre-populate cart with first item card
    addItem();
}
