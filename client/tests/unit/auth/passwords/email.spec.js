import { shallowMount } from "@vue/test-utils";
import Email from '@/Auth/Passwords/Email.vue'
import TestUtils from '../../../TestUtils'
import moxios from 'moxios'
import flushPromises from 'flush-promises'

describe ('Email.vue', () => {
  window.axios = require('axios')
  beforeEach(() => {
    moxios.install()
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('sends password reset link', async () => {
    const wrapper = shallowMount(Email)
    const testUtils = new TestUtils(wrapper)

    moxios.stubRequest('/api/password/email', {
      status: 200,
      response: {
        message: 'We have emailed your password reset link!'
      }
    })

    await testUtils.submit('#send-email-form')

    await flushPromises()

    testUtils.see('We have emailed your password reset link!')
  })
})