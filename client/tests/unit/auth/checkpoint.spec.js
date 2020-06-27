import Checkpoint from '@/Auth/Checkpoint.vue'
import { shallowMount, createLocalVue } from "@vue/test-utils"
import Vuex from 'vuex'
import TestUtils from '../../TestUtils'
import flushPromises from 'flush-promises'
import moxios from 'moxios'

describe ('Checkpoint.vue', () => {
  window.app = require('@/utils/app')

  it ('checks otp',  async () => {
    const localVue = createLocalVue()
    localVue.use(Vuex)

    const actions = {
      verify: jest.fn(() => Promise.resolve())
    }

    const $router = {
      replace: jest.fn()
    }

    const store = new Vuex.Store({
      modules: {
        login: {
          namespaced: true,
          state: {
            user: {
              otp_type: 'mail'
            }
          },
          getters: {
            user (state) {
              return state.user
            }
          }
        },
        checkpoint: {
          namespaced: true,
          actions
        }
      }
    })

    const wrapper = shallowMount(Checkpoint, { store, localVue, mocks: { $router } })
    const testUtils = new TestUtils(wrapper)

    await testUtils.submit('#checkpoint-form')
    await flushPromises()

    expect(actions.verify).toHaveBeenCalled()
    expect($router.replace).toHaveBeenCalledWith({ name: 'dashboard' })
  })

  it.only ('resends otp', async () => {
    window.axios = require('axios')
    moxios.install()

    const localVue = createLocalVue()
    localVue.use(Vuex)

    const store = new Vuex.Store({
      modules: {
        login: {
          namespaced: true,
          state: {
            user: {
              otp_type: 'mail'
            }
          },
          getters: {
            user (state) {
              return state.user
            }
          }
        }
      }
    })

    const wrapper = shallowMount(Checkpoint, { store, localVue })
    const testUtils = new TestUtils(wrapper)

    moxios.stubRequest('/api/checkpoint/resend', {
      status: 200,
      response: {
        message: 'OTP was resent.'
      }
    })

    await testUtils.click('#resend')
    await flushPromises()

    testUtils.see('OTP was resent.')

    moxios.uninstall()
  })

  it ('shows spinner and disables the resend button while resending', async () => {
    const localVue = createLocalVue()
    localVue.use(Vuex)

    const store = new Vuex.Store({
      modules: {
        login: {
          namespaced: true,
          state: {
            user: {
              otp_type: 'mail'
            }
          },
          getters: {
            user (state) {
              return state.user
            }
          }
        }
      }
    })

    const wrapper = shallowMount(Checkpoint, { store, localVue })

    wrapper.vm.isResending = true

    await wrapper.vm.$nextTick()

    expect(wrapper.contains('.resend-spinner')).toBe(true)
    expect(wrapper.find('#resend').attributes('disabled')).toBe("true")
  })
})