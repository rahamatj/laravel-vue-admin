import moxios from "moxios"
import flushPromises from "flush-promises"
import Users from '@/Users/Users.vue'
import { mount } from "@vue/test-utils"
import TestUtils from "../../TestUtils";

describe ('Users.vue', () => {
  it.only ('creates user', async () => {
    window.axios = require('axios')
    moxios.install()

    const wrapper = mount(Users)
    const testUtils = new TestUtils(wrapper)

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

    const bvModalEvent = {
      preventDefault: jest.fn()
    }

    await wrapper.vm.createUser(bvModalEvent)

    await flushPromises()

    testUtils.see('User created successfully!')

    moxios.uninstall()
  })
})