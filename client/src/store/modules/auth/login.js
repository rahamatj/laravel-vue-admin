import Fingerprint2 from 'fingerprintjs2'

export default {
  namespaced: true,
  state: {
    token: null,
    user: null,
    fingerprint: null,
    isLoggingOut: false
  },
  getters: {
    isAuthenticated (state) {
      return !!state.token
    },
    user (state) {
      return state.user
    },
    fingerprint (state) {
      return state.fingerprint
    },
    isLoggingOut (state) {
      return state.isLoggingOut
    }
  },
  mutations: {
    SET_TOKEN (state, token) {
      state.token = token
    },
    SET_USER (state, user) {
      state.user = user
    },
    SET_FINGERPRINT (state, fingerprint) {
      state.fingerprint = fingerprint
    },
    SET_IS_LOGGING_OUT (state, isLoggingOut) {
      state.isLoggingOut = isLoggingOut
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
            .catch(data => reject(data))
      })
    },
    unauthenticate({ commit }) {
      return new Promise((resolve, reject) => {
        commit('SET_IS_LOGGING_OUT', true)

        axios.post('/api/logout')
            .then(response => {
              commit('SET_TOKEN', null)
              commit('SET_FINGERPRINT', null)
              commit('SET_USER', null)
              commit('checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN', false, { root: true })
              commit('SET_IS_LOGGING_OUT', false)

              resolve()
            })
            .catch(error => {
              commit('SET_IS_LOGGING_OUT', false)

              reject(error)
            })
      })
    },
    check({ commit }) {
      return new Promise((resolve, reject) => {
        commit('SET_TOKEN', localStorage.getItem('token'))
        commit('SET_FINGERPRINT', localStorage.getItem('fingerprint'))
        commit('SET_USER', JSON.parse(localStorage.getItem('user')))
        commit(
            'checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN',
            localStorage.getItem('isOtpVerifiedAtLogin') === 'true',
            { root: true }
        )

        axios.get('/api/auth/check')
            .then(response => resolve(response.data))
            .catch(error => {
              commit('SET_TOKEN', null)
              commit('SET_FINGERPRINT', null)
              commit('SET_USER', null)
              commit(
                  'checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN',
                  false,
                  { root: true }
              )

              reject(error.response.data)
            })
      })
    }
  }
}