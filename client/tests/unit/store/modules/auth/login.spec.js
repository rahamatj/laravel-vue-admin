import login from '@/store/modules/auth/login'
import sinon from 'sinon'
import moxios from 'moxios'
import Form from "@/utils/Form";

const { mutations, actions, getters } = login

describe ('mutations', () => {
  let state;

  beforeEach(() => {
    state = {
      token: null,
      user: null,
      fingerprint: null,
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

  it ('sets is logging out', () => {
    mutations.SET_IS_LOGGING_OUT(state, true)

    expect(state.isLoggingOut).toBe(true)
  })
})

describe('getters', () => {
  it ('gets is authenticated true when token is set', () => {
    const state = { token: 'test' }

    const result = getters.isAuthenticated(state)

    expect(result).toBe(true)
  })

  it ('gets is authenticated false when token is not set', () => {
    const state = { token: null }

    const result = getters.isAuthenticated(state)

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

  it ('gets if logging out', () => {
    const state = { isLoggingOut: true }

    const result = getters.isLoggingOut(state)

    expect(result).toBe(true)
  })
})

describe('actions', () => {
  window.axios = require('axios')

  let commit

  beforeEach(() => {
    moxios.install()
    commit = sinon.spy()
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('authenticates user', async () => {
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
    moxios.stubRequest('/api/logout', { status: 204 })

    await actions.unauthenticate({ commit })

    expect(commit.args).toStrictEqual([
      ['SET_TOKEN', null],
      ['SET_FINGERPRINT', null],
      ['SET_USER', null],
      ['checkpoint/SET_IS_OTP_VERIFIED_AT_LOGIN', false, { root: true }]
    ])
  })
})