<template>
    <!-- validateEnquire populates this with any errors in form 
     replaces the createErrorBox fn in part2.js -->

    <div v-if="errMsgs.length > 0" id="enquire-errors" class="error-box">
        <p>Fix the following:</p>
        <ul>
            <!-- :key is an optimisation to tell vue only to update the error box if the msg changed
             see: https://vuejs.org/guide/essentials/list.html#maintaining-state-with-key -->
           <li v-for="msg in errMsgs" :key="msg">{{ msg }}</li>
        </ul>
    </div>
</template>

<script setup lang="ts">
const errMsgs = defineModel<string[]>({ required: true });
</script>
