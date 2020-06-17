import guards from '@/guards'
import store from '@/store'

describe('guards', () => {
  it ('redirects authenticated user to dashboard when accessing login page', () => {
    store.commit('login/SET_TOKEN', 'test')

    const to = {}
    const from = {}
    const next = jest.fn()
    guards.redirectToDashboard(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'dashboard' })
  })

  it ('redirects unauthenticated user to login page when accessing dashboard', () => {
    store.commit('login/SET_TOKEN', null)

    const to = {}
    const from = {}
    const next = jest.fn()
    guards.redirectToLogin(to, from, next)

    expect(next).toHaveBeenCalledWith({ name: 'login' })
  })
})