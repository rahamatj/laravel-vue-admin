import checkpoint from '@/store/modules/auth/checkpoint'
import moxios from 'moxios'
import sinon from 'sinon'
import Form from "../../../../../src/utils/Form"

const { mutations, getters, actions } = checkpoint

describe ('mutations', () => {
  let state

  beforeEach(() => {
    state = {
      isOtpVerifiedAtLogin: false
    }
  })

  it ('sets is otp verified at login', () => {
    mutations.SET_IS_OTP_VERIFIED_AT_LOGIN(state, true)

    expect(state.isOtpVerifiedAtLogin).toBe(true)
  })
})

describe ('getters', () => {
  it ('gets if otp is verified at login', () => {
    const state = { isOtpVerifiedAtLogin: true }

    const result = getters.isOtpVerifiedAtLogin(state)

    expect(result).toBe(true)
  })
})

describe ('actions', () => {
  window.axios = require('axios')

  let commit

  beforeEach(() => {
    moxios.install()
    commit = sinon.spy()
  })

  afterEach(() => {
    moxios.uninstall()
  })

  it ('verifies otp', async () => {
    moxios.stubRequest('/api/checkpoint', {
      status: 200,
      response: {
        message: 'OTP verified successfully!'
      }
    })

    await actions.verify({ commit }, new Form({ otp: '1234' }))

    expect(commit.args).toStrictEqual([
        ['SET_IS_OTP_VERIFIED_AT_LOGIN', true]
    ])
  })
})