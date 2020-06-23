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
    authenticate ({ commit }, form) {
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
    },
    unauthenticate({ commit }) {
      return new Promise((resolve, reject) => {
        axios.post('/api/logout')
            .then(response => {
              commit('SET_TOKEN', null)
              commit('SET_USER', null)

              resolve()
            })
            .catch(error => reject(error))
      })
    }
  }
}