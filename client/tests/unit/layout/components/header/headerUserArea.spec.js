import { createLocalVue, shallowMount } from "@vue/test-utils"
import HeaderUserArea from '@/Layout/Components/Header/HeaderUserArea.vue'
import Vuex from 'vuex'
import TestUtils from '../../../../TestUtils'

describe ('HeaderUSerArea.vue', () => {
  let wrapper
  let testUtils

  const state = {
    user: {
      name: 'Test User'
    }
  }

  const mutations = {
    SET_IS_LOGGING_OUT: jest.fn()
  }

  const actions = {
    unauthenticate: jest.fn(() => Promise.resolve())
  }

  const $router = {
    replace: jest.fn()
  }

  beforeEach(() => {
    const localVue = createLocalVue()
    localVue.use(Vuex)

    const store = new Vuex.Store({
      modules: {
        login: {
          namespaced: true,
          state,
          getters: {
            user (state) {
              return state.user
            }
          },
          mutations,
          actions
        }
      }
    })

    wrapper = shallowMount(HeaderUserArea, { localVue, store, mocks: { $router } })
    testUtils = new TestUtils(wrapper)
  })

  it ('shows the user name', () => {
    testUtils.see('Test User')
  })

  it ('logs out the user', async () => {
    testUtils.click('#logout')

    expect(mutations.SET_IS_LOGGING_OUT).toHaveBeenCalledWith(state, true)
    expect(actions.unauthenticate).toHaveBeenCalled()

    await wrapper.vm.$nextTick()

    expect(mutations.SET_IS_LOGGING_OUT).toHaveBeenCalledWith(state, false)
    expect($router.replace).toHaveBeenCalledWith({ name: 'login' })
  })
})