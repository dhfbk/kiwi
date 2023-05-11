<template>
    <h1>Web interface</h1>
    <div v-if="!loaded">
        <span class="spinner-border spinner-border-sm me-3"></span>
        Loading...
    </div>
    <div v-else class="row">
        <p>Interface ready</p>
    </div>
</template>

<script setup>
/* eslint-disable */

import { onMounted, inject, ref } from 'vue';

const axios = inject('axios')
const updateAxiosParams = inject('updateAxiosParams');

const loaded = ref(false);
const buttonDisabled = ref(true);

onMounted(async function() {
    loaded.value = true;
});

</script>

