import { shallowMount } from '@vue/test-utils';
import Clients from '@/Clients/Clients.vue'
import moxios from 'moxios'
import flushPromises from 'flush-promises'
import TestUtils from "../../TestUtils";

describe ('Clients.vue', () => {
  window.axios = require('axios')

  const datatableStub = {
    render: jest.fn(),
    methods: {
      refresh: jest.fn()
    }
  }

  beforeEach(() => {
    moxios.install()
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('changes enabled status', async () => {
    moxios.stubRequest('/api/clients/1/enabled', {
      status: 200,
      response: {
        message: 'Enabled status changed successfully!'
      }
    })

    const wrapper = shallowMount(Clients, { stubs: { 'datatable': datatableStub } })
    const testUtils = new TestUtils(wrapper)

    wrapper.vm.changeEnabledStatus(1)

    await flushPromises()

    testUtils.see('Enabled status changed successfully!')
  })

  it ('destroys client', async () => {
    moxios.stubRequest('/api/clients/1', {
      status: 200,
      response: {
        message: 'Client deleted successfully!'
      }
    })

    const $bvModal = {
      msgBoxConfirm: () => Promise.resolve(true)
    }

    const wrapper = shallowMount(Clients, { mocks: { $bvModal }, stubs: { 'datatable': datatableStub } })
    const testUtils = new TestUtils(wrapper)

    wrapper.vm.destroy(1)

    await flushPromises()

    testUtils.see('Client deleted successfully!')
  })
})