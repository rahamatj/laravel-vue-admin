import { shallowMount } from "@vue/test-utils";
import Reset from '@/Auth/Passwords/Reset.vue'
import TestUtils from '../../../TestUtils'
import flushPromises from 'flush-promises'
import moxios from 'moxios'

describe ('Reset.vue', () => {
  beforeEach(() => {
    moxios.install()
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('resets password', async () => {
    const wrapper = shallowMount(Reset)
    const testUtils = new TestUtils(wrapper)

    moxios.stubRequest('/api/password/reset', {
      status: 200,
      response: {
        message: 'Your password has been reset!'
      }
    })

    await testUtils.submit('#reset-password-form')

    await flushPromises()

    testUtils.see('Your password has been reset!')
  })
})