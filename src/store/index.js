import {createStore} from 'vuex'

export default createStore({
    state: {
        loggedIn: false,
    },
    getters: {},
    mutations: {
        sessionOnly(state, payload) {
            localStorage.setItem('php_sess_id', payload.sess_id);
            // state.loggedAdmin = payload.admin;
        },
        login(state, payload) {
            state.loggedIn = true;
            if (payload.sess_id !== undefined) {
                localStorage.setItem('php_sess_id', payload.sess_id);
            }
        },
        logout(state) {
            state.loggedIn = false;
            localStorage.removeItem('php_sess_id');
        }
    },
    actions: {},
    modules: {}
})
