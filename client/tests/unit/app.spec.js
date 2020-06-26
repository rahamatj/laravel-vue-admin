import { shallowMount, createLocalVue } from "@vue/test-utils";
import Vuex from 'vuex'
import App from '@/App.vue'

describe ('App.vue', () => {
  it ('shows spinner while logging out', () => {
    const localVue = createLocalVue()
    localVue.use(Vuex)

    const store = new Vuex.Store({
      modules: {
        login: {
          namespaced: true,
          state: {
            isLoggingOut: true
          },
          getters: {
            isLoggingOut (state) {
              return state.isLoggingOut
            }
          }
        }
      }
    })

    const $route = {
      meta: {
        layout: 'default'
      }
    }

    const wrapper = shallowMount(App, { store, localVue, mocks: { $route } })

    expect(wrapper.find('.spinner').exists()).toBe(true)
    expect(wrapper.find('.component').exists()).toBe(false)
  })

  it ('shows component while not logging out', () => {
    const localVue = createLocalVue()
    localVue.use(Vuex)

    const store = new Vuex.Store({
      modules: {
        login: {
          namespaced: true,
          state: {
            isLoggingOut: false
          },
          getters: {
            isLoggingOut (state) {
              return state.isLoggingOut
            }
          }
        }
      }
    })

    const $route = {
      meta: {
        layout: 'default'
      }
    }

    const wrapper = shallowMount(App, { store, localVue, mocks: { $route } })

    expect(wrapper.find('.spinner').exists()).toBe(false)
    expect(wrapper.find('.component').exists()).toBe(true)
  })
})