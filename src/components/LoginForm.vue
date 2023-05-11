<template>
    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col" id="loginForm">
                <div class="m-3 mb-5 text-center">
                    <img id="imglogo" src="kiwi.png" />
                </div>
                <div class="card p-5" id="form1">
                    <form class="form-data text-center">
                        <div class="text-start">
                            <div class="forms-inputs mb-3">
                                <label for="adminlogin">Username</label>
                                <input autocomplete="off" type="text" v-model="username"
                                       class="form-control" id="adminlogin"
                                       v-bind:class="{'is-invalid' : !validField(username) && usernameBlurred}"
                                       v-on:blur="usernameBlurred = true">
                                <div class="invalid-feedback">A valid username is required!</div>
                            </div>
                            <div class="forms-inputs mb-3">
                                <label for="adminpassword">Password</label>
                                <input autocomplete="off" type="password" v-model="password"
                                       class="form-control" id="adminpassword"
                                       v-bind:class="{'is-invalid' : !validField(password) && passwordBlurred}"
                                       v-on:blur="passwordBlurred = true">
                                <div class="invalid-feedback">A valid password is required!</div>
                            </div>
                        </div>
                        <div class="mb-3 mt-5">
                            <button v-on:click.stop.prevent="submit" class="btn btn-dark w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {defineEmits, ref} from 'vue'
const emit = defineEmits(['submit'])
const username = ref("");
const password = ref("");
const usernameBlurred = ref(false);
const passwordBlurred = ref(false);
// watch(username, (newValue) => {
//   console.log(newValue);
// });
function validField(content) {
    if (content.length > 0) {
        return true;
    }
    return false;
}
function submit() {
    usernameBlurred.value = true;
    passwordBlurred.value = true;
    if (validField(username.value) && validField(password.value)) {
        emit("submit", {username: username.value, password: password.value});
    }
}
</script>

<style>
#imglogo {
    width: 200px;
}
#loginForm {
    max-width: 500px;
}
</style>
