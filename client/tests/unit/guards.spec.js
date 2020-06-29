import guards from '@/guards'
import store from '@/store'

describe('guards', () => {
  let to, from, next

  beforeEach(() => {
    localStorage.clear()

    to = {}
    from = {}
    next = jest.fn()
  })

  it ('redirects authenticated user to dashboard when accessing login page', () => {
    store.commit('login/SET_TOKEN', 'test')

    guards.authenticate(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'dashboard' })
  })

  it ('redirects unauthenticated user to login page when accessing dashboard', () => {
    const user = {
      is_otp_verification_enabled_at_login: false
    }

    store.commit('login/SET_TOKEN', null)
    store.commit('login/SET_USER', user)
    store.commit('checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN', true)

    guards.accessApp(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'login' })
  })

  it ('redirects user to checkpoint if otp verification at login is enabled and otp is not verified when accessing dashboard', () => {
    const user = {
      is_otp_verification_enabled_at_login: true
    }

    store.commit('login/SET_TOKEN', null)
    store.commit('login/SET_USER', user)
    store.commit('checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN', false)

    guards.accessApp(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'checkpoint' })
  })

  it ('redirects user to dashboard if otp verification at login is disabled when accessing checkpoint', () => {
    const user = {
      is_otp_verification_enabled_at_login: false
    }

    store.commit('login/SET_USER', user)

    guards.verifyOtpAtLogin(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'dashboard' })
  })

  it ('redirects user to dashboard if otp is verified at login when accessing checkpoint', () => {
    const user = {
      is_otp_verification_enabled_at_login: true
    }

    store.commit('login/SET_USER', user)
    store.commit('checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN', true)

    guards.verifyOtpAtLogin(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'dashboard' })
  })

  it ('redirects user to activate google2fa if otp type is google2fa and google2fa is not activated when accessing checkpoint', () => {
    const user = {
      is_otp_verification_enabled_at_login: true,
      otp_type: 'google2fa',
      is_google2fa_activated: false
    }

    store.commit('login/SET_USER', user)

    guards.verifyOtpAtLogin(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'google2fa.activate' })
  })

  it ('redirects user to checkpoint if google2fa is activated when accessing activate google2fa', () => {
    const user = {
      is_google2fa_activated: true,
      otp_type: 'google2fa'
    }

    store.commit('login/SET_USER', user)

    guards.activateGoogle2fa(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'checkpoint' })
  })

  it ('redirects user to checkpoint if otp type is not google2fa when accessing activate google2fa', () => {
    const user = {
      is_google2fa_activated: false,
      otp_type: 'pin'
    }

    store.commit('login/SET_USER', user)

    guards.activateGoogle2fa(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'checkpoint' })
  })
})