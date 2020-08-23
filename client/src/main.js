import Vue from 'vue'
import router from './router'

import BootstrapVue from 'bootstrap-vue'

import App from './App'

import Default from './Layout/Wrappers/baseLayout.vue'
import Pages from './Layout/Wrappers/pagesLayout.vue'
import Datatable from './utils/Datatable/Datatable.vue'

import store from './store'

window.app = require('./utils/app')

window.axios = require('axios')

axios.defaults.baseURL = process.env.NODE_ENV === 'production'
    ? window.apiUrl
    : app.apiUrl

require('@/store/subscriber')

store.dispatch('login/check')
    .catch(data => console.error(data.message))

Vue.config.productionTip = false;

Vue.use(BootstrapVue)

Vue.component('default-layout', Default)
Vue.component('userpages-layout', Pages)
Vue.component('datatable', Datatable)

router.beforeEach((to, from, next) => {
  let title = process.env.NODE_ENV === 'production'
      ? window.appName
      : app.name

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
  components: {App}
});
