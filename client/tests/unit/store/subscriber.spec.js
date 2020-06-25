import store from '@/store'
require('@/store/subscriber')

describe ('subscriber', () => {
  window.axios = require('axios')

  beforeEach(() => {
    localStorage.clear()
  })

  it ('stores token to local storage', () => {
    store.commit('login/SET_TOKEN', 'test')

    expect(localStorage.getItem('token')).toBe('test')
  })

  it ('removes token from local storage if payload is null', () => {
    store.commit('login/SET_TOKEN', null)

    expect(localStorage.getItem('token')).toBe(null)
  })

  it ('stores user to local storage', () => {
    store.commit('login/SET_USER', { name: 'user', email: 'user@email.com' })

    expect(localStorage.getItem('user')).toBe(JSON.stringify({ name: 'user', email: 'user@email.com' }))
  })

  it ('removes user from local storage if payload is null', () => {
    store.commit('login/SET_USER', null)

    expect(localStorage.getItem('user')).toBe(null)
  })

  it ('stores fingerprint to local storage', () => {
    store.commit('login/SET_FINGERPRINT', 'test')

    expect(localStorage.getItem('fingerprint')).toBe('test')
  })

  it ('removes fingerprint from local storage if payload is null', () => {
    store.commit('login/SET_FINGERPRINT', null)

    expect(localStorage.getItem('fingerprint')).toBe(null)
  })
})