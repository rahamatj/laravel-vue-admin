import store from '@/store'

export default {
  authenticate: (to, from, next) => {
    if (store.getters['login/isAuthenticated'])
      return next({ name: 'dashboard' })

    return next()
  },
  accessApp: (to, from, next) => {
    if (! store.getters['login/isAuthenticated'])
      return next({ name: 'login' })

    if (store.getters['login/user'].is_otp_verification_enabled_at_login
      && ! store.getters['checkpoint/isOtpVerifiedAtLogin'])
      return next({ name: 'checkpoint' })

    return next()
  },
  verifyOtpAtLogin: (to, from, next) => {
    const user = store.getters['login/user']

    if (user === null)
      return next({ name: 'Login' })

    if (user.otp_type === 'google2fa' && ! user.is_google2fa_activated)
      return next({ name: 'google2fa.activate' })

    if (! store.getters['login/user'].is_otp_verification_enabled_at_login
      || store.getters['checkpoint/isOtpVerifiedAtLogin'])
      return next({ name: 'dashboard' })

    return next()
  },
  activateGoogle2fa: (to, from, next) => {
    const user = store.getters['login/user']

    if (user === null)
      return next({ name: 'Login' })

    if (user.is_google2fa_activated || user.otp_type !== 'google2fa')
      return next({ name: 'checkpoint' })

    return next()
  }
}