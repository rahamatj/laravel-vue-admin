export default {
  types: {
    pin: {
      prompt: 'Use your PIN as OTP.',
      resendable: false
    },
    mail: {
      prompt: 'We\'ve sent an OTP to your email.',
      resendable: true
    },
    sms: {
      prompt: 'We\'ve sent an OTP to your phone.',
      resendable: true
    },
    google2fa: {
      prompt: 'Use OTP from your Google Authenticator app.',
      resendable: false
    }
  },
  prompt (otpType) {
    return this.types[otpType].prompt
  },
  resendable (otpType) {
    return this.types[otpType].resendable
  }
}