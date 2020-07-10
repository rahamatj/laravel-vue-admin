import moxios from "moxios"
import flushPromises from "flush-promises"
import Users from '@/Users/Users.vue'
import { mount } from "@vue/test-utils"
import TestUtils from "../../TestUtils";

describe ('Users.vue', () => {
  window.axios = require('axios')

  let wrapper;
  let testUtils;

  beforeEach(() => {
    moxios.install()

    wrapper = mount(Users)
    testUtils = new TestUtils(wrapper)
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('creates user', async () => {
    moxios.stubRequest('/api/users', {
      status: 200,
      response: {
        message: 'User created successfully!',
        data: {
          name: 'test',
          email: 'test@email.com'
        }
      }
    })

    wrapper.vm.storeUser()

    await flushPromises()

    testUtils.see('User created successfully!')
  })

  it.only ('updates user', async () => {
    await wrapper.setData({
      editingUserId: 1
    })

    moxios.stubRequest('/api/users/1', {
      status: 200,
      response: {
        message: 'User updated successfully!'
      }
    })

    wrapper.vm.updateUser()

    await flushPromises()

    testUtils.see('User updated successfully!')
  })
})