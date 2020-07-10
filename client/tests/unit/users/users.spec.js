import moxios from "moxios"
import flushPromises from "flush-promises"
import Users from '@/Users/Users.vue'
import {mount, shallowMount} from "@vue/test-utils"
import TestUtils from "../../TestUtils";

describe ('Users.vue', () => {
  window.axios = require('axios')

  beforeEach(() => {
    moxios.install()
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

    const wrapper = mount(Users)
    const testUtils = new TestUtils(wrapper)

    wrapper.vm.store()

    await flushPromises()

    testUtils.see('User created successfully!')
  })

  it ('updates user', async () => {
    moxios.stubRequest('/api/users/1', {
      status: 200,
      response: {
        message: 'User updated successfully!'
      }
    })

    const wrapper = mount(Users)
    const testUtils = new TestUtils(wrapper)

    await wrapper.setData({
      id: 1
    })

    wrapper.vm.update()

    await flushPromises()

    testUtils.see('User updated successfully!')
  })

  it ('destroys user', async () => {
    moxios.stubRequest('/api/users/1', {
      status: 200,
      response: {
        message: 'User deleted successfully!'
      }
    })

    const $bvModal = {
      msgBoxConfirm: () => Promise.resolve(true)
    }

    const wrapper = shallowMount(Users, { mocks: { $bvModal } })
    const testUtils = new TestUtils(wrapper)

    wrapper.vm.destroy(1)

    await flushPromises()

    testUtils.see('User deleted successfully!')
  })
})