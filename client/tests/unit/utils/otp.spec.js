import otp from '@/utils/otp'

describe ('otp', () => {
  it ('returns prompt', () => {
    expect(otp.prompt('pin')).toBe('Use your PIN as OTP.')
  })

  it ('returns if it is resendable', () => {
    expect(otp.resendable('mail')).toBe(true)
  })
})