<template>
    <!-- NOTE: prices, extras, and most other selections come from JS now so only need to update consts.ts 
     rather than update HTML AND JS-->
    <section id="cart" class="cart">
        <h2><span class="icons-cart"></span>Cart</h2>
        <div id="cart-items" class="cart-list">
            <!-- field names follow card position ie item-1-product, item-1-capacity, item-1-quantity -> item-2-* so on -->
            <section v-for="(item, index) in cart" :key="item.id" class="cart-item cart-card">
                <div class="cart-card-header">
                    <h3 class="cart-card-title">Item <span class="cart-item-number">{{ index + 1 }}</span></h3>
                    <!-- hide delete button if there is only 1 item -->
                    <button v-if="cart.length > 1" type="button" class="icon-button cart-remove"
                        @click="removeItem(index)"><span class="icons-delete"></span></button>
                </div>

                <p class="cart-label">Product</p>
                <div class="cart-card-group" role="radiogroup">
                    <label v-for="(name, product) in PRODUCT_NAMES" :key="product" class="cart-card-group-item">
                        <input type="radio" :name="'item-' + (index + 1) + '-product'" :value="product"
                            v-model="item.product">
                        <span class="cart-card-group-item-body">
                            <span class="cart-card-group-item-name">{{ name }}</span>
                            <span class="cart-card-group-item-price">${{ PRODUCT_PRICES[product].toFixed(2) }}</span>
                        </span>
                    </label>
                </div>

                <div class="cart-field-row">
                    <label class="cart-field cart-field-select">
                        <select :name="'item-' + (index + 1) + '-capacity'" v-model="item.capacity">
                            <option value="">Choose storage</option>
                            <option v-for="(name, storage) in STORAGE_NAMES" :key="storage" :value="storage">
                                {{ name }} (+${{ STORAGE_PRICES[storage].toFixed(2) }})
                            </option>
                        </select>
                        <span class="cart-field-label">Storage</span>
                    </label>
                    <label class="cart-field cart-field-quantity">
                        <input type="text" inputmode="numeric" :name="'item-' + (index + 1) + '-quantity'"
                            v-model="item.quantity">
                        <span class="cart-field-label">Quantity</span>
                    </label>
                </div>
            </section>

            <!-- add item card, always last box in list -->
            <button type="button" id="add-item" class="cart-add" @click="addItem">
                <span class="cart-add-icon icons-add"></span>
                <span class="cart-add-text">Add item</span>
            </button>
        </div>
    </section>

    <!-- extras apply once to the whole order, no selection required -->
    <fieldset>
        <legend>Order extras</legend>
        <label v-for="(name, extra) in EXTRA_NAMES" :key="extra">
            <input type="checkbox" name="extras" :value="extra" v-model="extras">
            {{ name }} (${{ EXTRA_PRICES[extra].toFixed(2) }})
        </label>
    </fieldset>

    <div>
        <label for="comment">Comment</label>
        <textarea id="comment" name="comment" placeholder="Enter other instructions or requests if desired..."
            v-model="comment"></textarea>
    </div>
</template>

<script setup lang="ts">
import { EXTRA_NAMES, EXTRA_PRICES, PRODUCT_NAMES, PRODUCT_PRICES, STORAGE_NAMES, STORAGE_PRICES } from "./consts";
import type { CartItem } from "./types";

// owned by Enquire2.vue, which validates and stores them on submit
// defineModel -> links to v-model in Enquire2.vue to pass data up and down the tree. required: true -> must be passed in
const cart = defineModel<CartItem[]>("cart", { required: true });
const extras = defineModel<string[]>("extras", { required: true });
const comment = defineModel<string>("comment", { required: true });

// used to send to payment page, which is not in the framework
let nextItemId = 1;

function addItem() {
    cart.value.push({ product: "", capacity: "", quantity: "1" });
}

function removeItem(index: number) {
    cart.value.splice(index, 1);
}

// pre-populate cart with first item card
addItem();
</script>
