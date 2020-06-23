import Vue from 'vue'
import router from './router'

import BootstrapVue from "bootstrap-vue"

import App from './App'

import Default from './Layout/Wrappers/baseLayout.vue';
import Pages from './Layout/Wrappers/pagesLayout.vue';

import store from './store'

require('@/store/subscriber')

store.commit('login/SET_TOKEN', localStorage.getItem('token'))
store.commit('login/SET_USER', JSON.parse(localStorage.getItem('user')))

Vue.config.productionTip = false;

Vue.use(BootstrapVue);

Vue.component('default-layout', Default);
Vue.component('userpages-layout', Pages);

router.beforeEach((to, from, next) => {
  let title = 'Eload'

  if (to.meta.title)
    title += ' - ' + to.meta.title

  document.title = title
  next()
})

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
});
