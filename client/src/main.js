import Vue from 'vue'
import router from './router'

import BootstrapVue from "bootstrap-vue"

import App from './App'

import Default from './Layout/Wrappers/baseLayout.vue';
import Pages from './Layout/Wrappers/pagesLayout.vue';

import store from './store'

window.app = require('./utils/app')

window.axios = require('axios');

require('@/store/subscriber')

store.commit('login/SET_TOKEN', localStorage.getItem('token'))
store.commit('login/SET_FINGERPRINT', localStorage.getItem('fingerprint'))
store.commit('login/SET_USER', JSON.parse(localStorage.getItem('user')))
store.commit(
    'checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN',
    localStorage.getItem('isOtpVerifiedAtLogin') === 'true'
)

Vue.config.productionTip = false;

Vue.use(BootstrapVue);

Vue.component('default-layout', Default);
Vue.component('userpages-layout', Pages);

router.beforeEach((to, from, next) => {
  let title = app.name

  if (to.meta.title)
    title += ' - ' + to.meta.title

  document.title = title
  next()
})

new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
});
