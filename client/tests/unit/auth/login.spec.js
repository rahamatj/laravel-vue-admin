import {createLocalVue, shallowMount} from '@vue/test-utils'
import Login from '@/Auth/Login.vue'
import TestUtils from '../../TestUtils'
import Vuex from 'vuex'
import Errors from "../../../src/utils/Errors"
import flushPromises from 'flush-promises'

describe ('Login.vue', () => {
  it ('authenticates and redirects to dashboard', async () => {
    const localVue = createLocalVue()
    localVue.use(Vuex)

    const actions = {
      authenticate: jest.fn(() => Promise.resolve())
    }

    const $router = {
      replace: jest.fn()
    }

    const store = new Vuex.Store({ modules: { login: { namespaced: true, actions } } })
    const wrapper = shallowMount(Login, { store, localVue, mocks: { $router } })
    const testUtils = new TestUtils(wrapper)

    await testUtils.submit('#login-form')

    await flushPromises()

    expect(actions.authenticate).toHaveBeenCalled()
    expect($router.replace).toHaveBeenCalledWith({ name: 'dashboard' })
  })

  it ('shows the current year', () => {
    const wrapper = shallowMount(Login)
    const testUtils = new TestUtils(wrapper)

    testUtils.text((new Date().getFullYear()))
  })

  it ('shows errors if any', async () => {
    const wrapper = shallowMount(Login)
    const testUtils = new TestUtils(wrapper)

    wrapper.setData({
      form: {
        errors: new Errors()
      }
    })

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {
        email: ['The email field is required.'],
        password: ['The password field is required.']
      }
    })

    await wrapper.vm.$nextTick()

    testUtils.see('The given data was invalid.')
    testUtils.see('The email field is required.')
    testUtils.see('The password field is required.')
  })

  it ('removes error when the error is fixed', async () => {
    const wrapper = shallowMount(Login)
    const testUtils = new TestUtils(wrapper)

    wrapper.setData({
      form: {
        errors: new Errors()
      }
    })

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {
        email: ['The email field is required.'],
        password: ['The password field is required.']
      }
    })

    await wrapper.vm.$nextTick();

    testUtils.see('The given data was invalid.')
    testUtils.see('The email field is required.')
    testUtils.see('The password field is required.')

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {}
    })

    await wrapper.vm.$nextTick();
    testUtils.doNotSee('The email field is required.')
    testUtils.doNotSee('The password field is required.')
  })

  it ('disables the submit button if there are any errors', async () => {
    const wrapper = shallowMount(Login)

    wrapper.setData({
      form: {
        errors: new Errors()
      }
    })

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {
        email: ['The email field is required.'],
        password: ['The password field is required.']
      }
    })

    await wrapper.vm.$nextTick();

    const loginButton = wrapper.find('#login')
    expect(loginButton.attributes('disabled')).toBe("true")
  })

  it ('re-enables the submit button if the errors are fixed', async () => {
    const wrapper = shallowMount(Login)

    wrapper.setData({
      form: {
        errors: new Errors()
      }
    })

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {
        email: ['The email field is required.'],
        password: ['The password field is required.']
      }
    })

    await wrapper.vm.$nextTick();

    const loginButton = wrapper.find('#login')
    expect(loginButton.attributes('disabled')).toBe("true")

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {}
    })

    await wrapper.vm.$nextTick();
    expect(loginButton.attributes('disabled')).toBe(undefined)
  })

  it ('removes the message when there are no errors', async () => {
    const wrapper = shallowMount(Login)
    const testUtils = new TestUtils(wrapper)

    wrapper.setData({
      form: {
        errors: new Errors()
      }
    })

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {
        email: ['The email field is required.'],
        password: ['The password field is required.']
      }
    })

    await wrapper.vm.$nextTick();

    testUtils.see('The given data was invalid.')

    wrapper.vm.form.errors.record({
      message: 'The given data was invalid.',
      errors: {}
    })

    expect(wrapper.vm.form.errors.hasMessage()).toBe(false)
  })

  it ('gets client info when created', () => {
    const getClientInfo = jest.fn()
    const wrapper = shallowMount(Login, { methods: { getClientInfo } })

    expect(getClientInfo).toHaveBeenCalled()
  })

  it ('sets client info', () => {
    const wrapper = shallowMount(Login)

    wrapper.vm.getClientInfo()

    expect(wrapper.vm.fingerprint).not.toBe('')
    expect(wrapper.vm.client).not.toBe('')
    expect(wrapper.vm.platform).not.toBe('')
  })

  it ('shows spinner and disables the submit while form is loading', async () => {
    const wrapper = shallowMount(Login)

    wrapper.vm.form.loading = true

    await wrapper.vm.$nextTick()

    expect(wrapper.contains('.spinner')).toBe(true)
    expect(wrapper.find('#login').attributes('disabled')).toBe("true")
  })
})
