import Activate from '@/Auth/Google2fa/Activate.vue'
import { shallowMount, createLocalVue } from "@vue/test-utils";
import moxios from 'moxios'
import TestUtils from '../../../TestUtils'
import flushPromises from 'flush-promises'
import Vuex from 'vuex'

describe ('Activate.vue', () => {
  window.app = require('@/utils/app')
  window.axios = require('axios')

  beforeEach(() => {
    moxios.install()

    moxios.stubRequest('/api/checkpoint/google2fa/activate', {
      status: 200,
      response: {
        g2faUrl: 'test-g2fa-url'
      }
    })
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('gets g2fa url when created', () => {
    const getG2faUrl = jest.fn()

    const wrapper = shallowMount(Activate, { methods: { getG2faUrl } })

    expect(getG2faUrl).toHaveBeenCalled()
  })

  it ('gets g2fa url', async () => {
    const wrapper = shallowMount(Activate)
    const testUtils = new TestUtils(wrapper)

    await flushPromises()

    testUtils.see('<img src="test-g2fa-url">')
  })

  it ('shows spinner while getting g2fa url and disables the ok button', async () => {
    const wrapper = shallowMount(Activate)

    wrapper.vm.isGettingG2faUrl = true

    await wrapper.vm.$nextTick()

    expect(wrapper.find('.spinner').exists()).toBe(true)
    expect(wrapper.find('.qrcode').exists()).toBe(false)
    const okButton = wrapper.find('#ok')
    expect(okButton.attributes('disabled')).toBe("true")
  })

  it.only ('redirects to checkpoint when ok button is clicked', () => {
    const localVue = createLocalVue()
    localVue.use(Vuex)

    const state = {
      user: {
        is_google2fa_activated: false
      }
    }

    const store = new Vuex.Store({
      modules: {
        login: {
          namespaced: true,
          state,
          mutations: {
            SET_USER (state, user) {
              state.user = user
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

    const $router = {
      replace: jest.fn()
    }

    const wrapper = shallowMount(Activate, { store, localVue, mocks: { $router } })
    const testUtils = new TestUtils(wrapper)

    testUtils.click('#ok')

    expect($router.replace).toHaveBeenCalledWith({ name: 'checkpoint' })
    expect(state.user.is_google2fa_activated).toBe(true)
  })
})