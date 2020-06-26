import login from '@/store/modules/auth/login'
import sinon from 'sinon'
import moxios from 'moxios'
import Form from "../../../../../src/utils/Form";

const { mutations, actions, getters } = login

describe('getters', () => {
  it ('gets authenticated true when token is set', () => {
    const state = { token: 'test' }

    const result = getters.authenticated(state)

    expect(result).toBe(true)
  })

  it ('gets authenticated false when token is not set', () => {
    const state = { token: null }

    const result = getters.authenticated(state)

    expect(result).toBe(false)
  })

  it ('gets authenticated user', () => {
    const state = { user: { name: 'user', email: 'user@email.com' } }

    const result = getters.user(state)

    expect(result).toStrictEqual({ name: 'user', email: 'user@email.com' })
  })

  it ('gets fingerprint', () => {
    const state = { fingerprint: 'test' }

    const result = getters.fingerprint(state)

    expect(result).toBe('test')
  })

  it ('gets if otp is verified at login', () => {
    const state = { isOtpVerifiedAtLogin: true }

    const result = getters.isOtpVerifiedAtLogin(state)

    expect(result).toBe(true)
  })

  it ('gets if logging out', () => {
    const state = { isLoggingOut: true }

    const result = getters.isLoggingOut(state)

    expect(result).toBe(true)
  })
})

describe ('mutations', () => {
  let state;

  beforeEach(() => {
    state = {
      token: null,
      user: null,
      fingerprint: null,
      isOtpVerifiedAtLogin: false,
      isLoggingOut: false
    }
  })

  it ('sets token', () => {
    mutations.SET_TOKEN(state, 'test')

    expect(state.token).toBe('test')
  })

  it ('sets user', () => {
    mutations.SET_USER(state, { name: 'user', email: 'user@email.com' })

    expect(state.user).toStrictEqual({ name: 'user', email: 'user@email.com' })
  })

  it ('sets fingerprint', () => {
    mutations.SET_FINGERPRINT(state, 'test')

    expect(state.fingerprint).toBe('test')
  })

  it ('sets is otp verified at login', () => {
    mutations.SET_IS_OTP_VERIFIED_AT_LOGIN(state, true)

    expect(state.isOtpVerifiedAtLogin).toBe(true)
  })

  it ('sets is logging out', () => {
    mutations.SET_IS_LOGGING_OUT(state, true)

    expect(state.isLoggingOut).toBe(true)
  })
})

describe('actions', () => {
  window.axios = require('axios')
  beforeEach(() => {
    moxios.install()
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('authenticates user', async () => {
    const commit = sinon.spy()

    moxios.stubRequest('/api/login', {
      status: 200,
      response: {
        message: 'Login successful!',
        token: 'test',
        user: {
          name: 'Admin',
          email: 'admin@email.com'
        }
      }
    })

    await actions.authenticate({ commit }, new Form({
      email: 'admin@email.com',
      password: '12345678'
    }))

    expect(commit.args).toStrictEqual([
        ['SET_TOKEN', 'test'],
        ['SET_USER', {
          name: 'Admin',
          email: 'admin@email.com'
        }]
    ])
  })

  it ('unauthenticates user', async () => {
    const commit = sinon.spy()

    moxios.stubRequest('/api/logout', { status: 204 })

    await actions.unauthenticate({ commit })

    expect(commit.args).toStrictEqual([
      ['SET_TOKEN', null],
      ['SET_USER', null]
    ])
  })
})