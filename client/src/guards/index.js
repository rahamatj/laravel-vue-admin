import store from '@/store'

export default {
  redirectToDashboard: (to, from, next) => {
    if(store.getters['login/authenticated'])
      next({ name: 'dashboard' })

    next()
  },
  redirectToLogin: (to, from, next) => {
    if(!store.getters['login/authenticated'])
      next({ name: 'login' })

    next()
  }
}