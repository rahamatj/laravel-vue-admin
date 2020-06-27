export default {
  namespaced: true,
  state: {
    isOtpVerifiedAtLogin: false
  },
  getters: {
    isOtpVerifiedAtLogin (state) {
      return state.isOtpVerifiedAtLogin
    }
  },
  mutations: {
    SET_IS_OTP_VERIFIED_AT_LOGIN (state, isOtpVerifiedAtLogin) {
      state.isOtpVerifiedAtLogin = isOtpVerifiedAtLogin
    }
  },
  actions: {
    verify ({ commit }, form) {
      return new Promise((resolve, reject) => {
        form.post('/api/checkpoint')
            .then(data => {
              commit('SET_IS_OTP_VERIFIED_AT_LOGIN', true)

              resolve(data)
            })
            .catch(data => reject(data))
      })
    }
  }
}