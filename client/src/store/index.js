import Vue from 'vue'
import Vuex from 'vuex'

import login from './modules/auth/login'
import checkpoint from './modules/auth/checkpoint'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    login,
    checkpoint
  }
})