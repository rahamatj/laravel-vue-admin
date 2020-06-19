import { createLocalVue, shallowMount } from "@vue/test-utils"
import HeaderUserArea from '@/Layout/Components/Header/HeaderUserArea.vue'
import Vuex from 'vuex'
import TestUtils from '../../../../TestUtils'

describe ('HeaderUSerArea.vue', () => {
  let wrapper
  let testUtils
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
          state: {
            user: {
              name: 'Test User'
            }
          },
          getters: {
            user (state) {
              return state.user
            }
          },
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

  it.only ('logs out the user', async () => {
    testUtils.click('#logout')
    expect(actions.unauthenticate).toHaveBeenCalled()

    await wrapper.vm.$nextTick()

    expect($router.replace).toHaveBeenCalledWith({ name: 'login' })
  })
})