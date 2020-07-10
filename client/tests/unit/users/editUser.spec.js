import EditUser from '@/Users/EditUser.vue'
import { shallowMount, mount } from '@vue/test-utils'
import moxios from 'moxios'
import flushPromises from 'flush-promises'
import TestUtils from '../../TestUtils'

describe ('EditUser.vue', () => {
  window.axios = require('axios')

  beforeEach(() => {
    moxios.install()

    moxios.stubRequest('/api/users/1', {
      status: 200,
      response: {
        data: {
          name: 'test',
          email: 'test@email.com',
          mobile_number: '123456',
          is_otp_verification_enabled_at_login: false,
          otp_type: 'pin',
          is_client_lock_enabled: false,
          clients_allowed: 1,
          is_ip_lock_enabled: false
        }
      }
    })
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('gets user when created', async () => {
    const wrapper = shallowMount(EditUser, {
      propsData: {
        id: 1
      }
    })

    await flushPromises()

    expect(wrapper.vm.form.name).toBe('test')
    expect(wrapper.vm.form.email).toBe('test@email.com')
    expect(wrapper.vm.form.mobile_number).toBe('123456')
    expect(wrapper.vm.form.is_otp_verification_enabled_at_login).toBe(false)
    expect(wrapper.vm.form.otp_type).toBe('pin')
    expect(wrapper.vm.form.is_client_lock_enabled).toBe(false)
    expect(wrapper.vm.form.clients_allowed).toBe(1)
    expect(wrapper.vm.form.is_ip_lock_enabled).toBe(false)
  })

  it ('updates password', async () => {
    moxios.stubRequest('/api/users/1/password', {
      status: 200,
      response: {
        message: 'Password updated successfully!'
      }
    })

    const wrapper = mount(EditUser, { propsData: { id: 1 } })
    const testUtils = new TestUtils(wrapper)

    await flushPromises()

    wrapper.vm.updatePassword()

    await flushPromises()

    testUtils.see('Password updated successfully!')
  })

  it.only ('updates pin', async () => {
    moxios.stubRequest('/api/users/1/pin', {
      status: 200,
      response: {
        message: 'PIN updated successfully!'
      }
    })

    const wrapper = mount(EditUser, { propsData: { id: 1 } })
    const testUtils = new TestUtils(wrapper)

    await flushPromises()

    wrapper.vm.updatePin()

    await flushPromises()

    testUtils.see('PIN updated successfully!')
  })
})