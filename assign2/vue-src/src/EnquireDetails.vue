<template>
    <h2><span class="icons-person"></span>Details</h2>

    <div>
        <label for="first-name">First name</label>
        <div class="field">
            <input type="text" id="first-name" name="first-name" v-model="details.firstName">
        </div>
    </div>
    <div>
        <label for="last-name">Last name</label>
        <div class="field">
            <input type="text" id="last-name" name="last-name" v-model="details.lastName">
        </div>
    </div>
    <div>
        <label for="email">Email address</label>
        <div class="field">
            <input type="text" id="email" name="email" inputmode="email" v-model="details.email">
        </div>
    </div>

    <fieldset>
        <legend>Address</legend>
        <div>
            <!-- NOTE: cannot enter unit number for townhouse or apartment ie 2/22 asc street. Letter of the assignment spec. -->
            <label for="street-address">Street address</label>
            <div class="field">
                <input type="text" id="street-address" name="street-address" v-model="details.street">
            </div>
        </div>
        <div>
            <label for="suburb">Suburb/town</label>
            <div class="field">
                <input type="text" id="suburb" name="suburb" v-model="details.suburb">
            </div>
        </div>
        <div>
            <label for="state">State</label>
            <select id="state" name="state" v-model="details.state">
                <option value="">-- Choose an option --</option>
                <!-- getting the state names from the postcode validator record -->
                <option v-for="state in Object.keys(STATE_VALID_DIGITS)" :key="state" :value="state">
                    {{ state }}
                </option>
            </select>
        </div>
        <div>
            <label for="postcode">Postcode</label>
            <div class="field">
                <input type="text" id="postcode" name="postcode" inputmode="numeric" v-model="details.postcode">
            </div>
        </div>
    </fieldset>

    <div>
        <label for="phone">Phone number</label>
        <div class="field">
            <input type="text" id="phone" name="phone" inputmode="numeric" placeholder="0123 456 789"
                v-model="details.phone">
        </div>
    </div>

    <fieldset>
        <legend>Preferred contact method</legend>
        <label>
            <input type="radio" name="preferred-contact" value="email" v-model="details.contact">
            Email
        </label>
        <label>
            <input type="radio" name="preferred-contact" value="post" v-model="details.contact">
            Post
        </label>
        <label>
            <input type="radio" name="preferred-contact" value="phone" v-model="details.contact">
            Phone
        </label>
    </fieldset>
</template>

<script setup lang="ts">
import { STATE_VALID_DIGITS } from "./consts";
import type { Details } from "./types";

// owned by Enquire2.vue, which validates and stores it on submit
const details = defineModel<Details>({ required: true });
</script>
