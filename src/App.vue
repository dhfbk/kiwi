<template>
    <div v-if="mainLoaded">
        <LoginForm v-if="!loggedIn" @submit="submit"/>
        <div v-else>
            <div class="container-lg">
                <NavBar :username="username"/>
                <div class="my-3">
                    <router-view/>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="spinner-border m-5" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</template>

<script setup>
/* eslint-disable */

import {computed, inject, onMounted, ref} from 'vue'
import {useStore} from 'vuex'
import LoginForm from '@/components/LoginForm.vue'
import NavBar from '@/components/NavBar.vue'

const mainLoaded = ref(false);
const axios = inject('axios')
const updateAxiosParams = inject('updateAxiosParams');
const store = useStore();
const username = ref("");

const loggedIn = computed(() => store.state.loggedIn);

function submit({username, password}) {
    axios.get("?", {
        "params": {"action": "login", "username": username, "password": password}
    })
        .then((response) => {
            let sess_id = response.data.session_id;
            store.commit("sessionOnly", {"sess_id": sess_id});
            loadUserInfo();
        })
        .catch((reason) => {
            alert(reason.response.data?.error
                ? reason.response.data.error
                : reason.response.statusText);
        });
}

async function loadUserInfo() {
    await axios.get("?", {"params": {"action": "userinfo", ...updateAxiosParams()}})
        .then((response) => {
            store.commit("login", response.data);
            username.value = response.data.data.username;
        })
        .catch((reason) => {
            store.commit('logout');
            if (reason.response && reason.response.status !== 401) {
                alert(reason.response.statusText);
            }
        })
        .then(() => {
            // mainLoaded.value = true;
        });
}

onMounted(async function () {
    await loadUserInfo();
    mainLoaded.value = true;
});

</script>

<style>
</style>
