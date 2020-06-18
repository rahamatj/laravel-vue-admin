export default class TestUtils {
  constructor(wrapper) {
    this.wrapper = wrapper
  }

  input(selector, value) {
    let input = this.wrapper.find(selector)
    input.element.value = value
    return input.trigger('input')
  }

  click(selector) {
    return this.wrapper.find(selector).trigger('click')
  }

  submit(selector) {
    return this.wrapper.find(selector).trigger('submit')
  }

  keyDown (selector) {
    return this.wrapper.find(selector).trigger('keydown')
  }

  text (text) {
    expect(this.wrapper.text()).toContain(text)
  }

  see (html) {
    expect(this.wrapper.html()).toContain(html)
  }

  doNotSee (html) {
    expect(this.wrapper.html()).not.toContain(html)
  }
}