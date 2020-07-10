import store from '@/store'

export default {
  authenticate: (to, from, next) => {
    if (store.getters['login/isAuthenticated'])
      next({ name: 'dashboard' })

    next()
  },
  accessApp: (to, from, next) => {
    if (! store.getters['login/isAuthenticated'])
      next({ name: 'login' })

    if (store.getters['login/user'].is_otp_verification_enabled_at_login
      && ! store.getters['checkpoint/isOtpVerifiedAtLogin'])
      next({ name: 'checkpoint' })

    next()
  },
  verifyOtpAtLogin: (to, from, next) => {
    const user = store.getters['login/user']

    if (user.otp_type === 'google2fa' && ! user.is_google2fa_activated)
      next({ name: 'google2fa.activate' })

    if (! store.getters['login/user'].is_otp_verification_enabled_at_login
      || store.getters['checkpoint/isOtpVerifiedAtLogin'])
      next({ name: 'dashboard' })

    next()
  },
  activateGoogle2fa: (to, from, next) => {
    const user = store.getters['login/user']

    if (user.is_google2fa_activated || user.otp_type !== 'google2fa')
      next({ name: 'checkpoint' })

    next()
  }
}