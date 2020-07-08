import CreateUser from '@/Users/CreateUser.vue'
import { shallowMount } from "@vue/test-utils"
import Form from '@/utils/Form/Form'
import moxios from 'moxios'
import flushPromises from 'flush-promises'

describe ('CreateUser.vue', () => {
  let wrapper;

  beforeEach(() => {
    wrapper = shallowMount(CreateUser)
  })

  it ('shows otp options if otp verification is enabled at login', async () => {
    wrapper.setData({
      form: new Form({
        is_otp_verification_enabled_at_login: true
      })
    })

    await wrapper.vm.$nextTick()

    expect(wrapper.find('.otp-options').exists()).toBe(true)
  })

  it ('does not show otp options if otp verification is disabled at login', async () => {
    wrapper.setData({
      form: new Form({
        is_otp_verification_enabled_at_login: false
      })
    })

    await wrapper.vm.$nextTick()

    expect(wrapper.find('.otp-options').exists()).toBe(false)
  })

  it ('shows pin input if otp type is pin', async () => {
    wrapper.setData({
      form: new Form({
        is_otp_verification_enabled_at_login: true,
        otp_type: 'pin'
      })
    })

    await wrapper.vm.$nextTick()

    expect(wrapper.find('#pin').exists()).toBe(true)
  })

  it ('does not show pin input if otp type is not pin', async () => {
    wrapper.setData({
      form: new Form({
        is_otp_verification_enabled_at_login: true,
        otp_type: 'mail'
      })
    })

    await wrapper.vm.$nextTick()

    expect(wrapper.find('#pin').exists()).toBe(false)
  })

  it ('shows clients allowed input if client lock is enabled', async () => {
    wrapper.setData({
      form: new Form({
        is_client_lock_enabled: true
      })
    })

    await wrapper.vm.$nextTick()

    expect(wrapper.find('.clients-allowed').exists()).toBe(true)
  })

  it ('does not show clients allowed input if client lock is disabled', async () => {
    wrapper.setData({
      form: new Form({
        is_client_lock_enabled: false
      })
    })

    await wrapper.vm.$nextTick()

    expect(wrapper.find('.clients-allowed').exists()).toBe(false)
  })
})