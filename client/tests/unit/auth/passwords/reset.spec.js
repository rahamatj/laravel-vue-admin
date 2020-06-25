import { shallowMount } from "@vue/test-utils";
import Reset from '@/Auth/Passwords/Reset.vue'
import TestUtils from '../../../TestUtils'
import flushPromises from 'flush-promises'
import moxios from 'moxios'

describe ('Reset.vue', () => {
  let wrapper
  let testUtils
  window.axios = require('axios')

  beforeEach(() => {
    moxios.install()

    wrapper = shallowMount(Reset)
    testUtils = new TestUtils(wrapper)

    moxios.stubRequest('/api/password/reset', {
      status: 200,
      response: {
        message: 'Your password has been reset!'
      }
    })
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('resets password', async () => {
    await testUtils.submit('#reset-password-form')

    await flushPromises()

    testUtils.see('Your password has been reset!')
  })

  it.only ('Login button appears after password has been reset', async () => {
    await testUtils.submit('#reset-password-form')

    await flushPromises()

    testUtils.see('Login')
  })
})