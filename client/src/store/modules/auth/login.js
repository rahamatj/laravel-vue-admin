export default {
  namespaced: true,
  state: {
    token: null,
    user: null
  },
  getters: {
    authenticated (state) {
      return !!state.token
    },
    user (state) {
      return state.user
    }
  },
  mutations: {
    SET_TOKEN (state, token) {
      state.token = token
    },
    SET_USER (state, user) {
      state.user = user
    }
  },
  actions: {
    getToken ({ commit }, form) {
      return new Promise((resolve, reject) => {
        form.post('/api/login')
            .then(data => {
              commit('SET_TOKEN', data.token)
              commit('SET_USER', data.user)

              resolve(data)
            })
            .catch(data => {
              reject(data)
            })
      })
    }
  }
}