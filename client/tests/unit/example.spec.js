import { shallowMount } from '@vue/test-utils'
import Analytics from '@/DemoPages/Dashboards/Analytics.vue'

describe('Analytics.vue', () => {
  it('renders Data Statistics', () => {
    const wrapper = shallowMount(Analytics)
    expect(wrapper.text()).toMatch('Data Statistics')
  })
})
