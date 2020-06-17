import login from '@/store/modules/auth/login'
import sinon from 'sinon'
import moxios from 'moxios'
import Form from "../../../../../src/utils/Form";

const { mutations, actions, getters } = login

describe ('mutations', () => {
  let state;

  beforeEach(() => {
    state = { token: null, user: null }
  })

  it ('sets token', () => {
    mutations.SET_TOKEN(state, 'test')
    expect(state.token).toBe('test')
  })

  it ('sets user', () => {
    mutations.SET_USER(state, { name: 'user', email: 'user@email.com' })
    expect(state.user).toStrictEqual({ name: 'user', email: 'user@email.com' })
  })
})

describe('actions', () => {
  beforeEach(() => {
    moxios.install()
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('gets token', async () => {
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

    await actions.getToken({ commit }, new Form({
      email: 'admin@email.com',
      password: '12345678',
      fingerprint: 'test',
      client: 'test',
      platform: 'test',
    }))

    expect(commit.args).toStrictEqual([
        ['SET_TOKEN', 'test'],
        ['SET_USER', {
          name: 'Admin',
          email: 'admin@email.com'
        }]
    ])
  })
})

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
})