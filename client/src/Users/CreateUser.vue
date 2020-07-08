<template>
    <div>
        <b-alert :show="form.errors.hasMessage()"
                 variant="danger"
                 dismissible
        >
            {{ form.errors.getMessage() }}
        </b-alert>
        <b-form @keydown="form.errors.clear($event.target.name)">
            <b-form-group id="name-input-group"
                          label="Name"
                          label-for="name">
                <b-form-input id="name"
                              type="text"
                              v-model="form.name"
                              name="name"
                              placeholder="Enter name">
                </b-form-input>
                <b-form-invalid-feedback
                        :state="!form.errors.has('name')"
                        v-text="form.errors.get('name')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <b-form-group id="email-input-group"
                          label="Email"
                          label-for="email">
                <b-form-input id="email"
                              type="email"
                              v-model="form.email"
                              name="email"
                              placeholder="Enter email">
                </b-form-input>
                <b-form-invalid-feedback
                        :state="!form.errors.has('email')"
                        v-text="form.errors.get('email')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <b-form-group id="mobile-number-input-group"
                          label="Mobile Number"
                          label-for="mobile-number"
                          description="Please include country code. e.g: +8801xxxxxxxx">
                <b-form-input id="mobile-number"
                              type="text"
                              v-model="form.mobile_number"
                              name="mobile_number"
                              placeholder="Enter mobile number">
                </b-form-input>
                <b-form-invalid-feedback
                        :state="!form.errors.has('mobile_number')"
                        v-text="form.errors.get('mobile_number')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <b-form-group id="password-input-group"
                          label="Password"
                          label-for="password">
                <b-form-input id="password"
                              type="password"
                              v-model="form.password"
                              name="password"
                              placeholder="Enter password">
                </b-form-input>
                <b-form-invalid-feedback
                        :state="!form.errors.has('password')"
                        v-text="form.errors.get('password')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <b-form-group id="password-confirm-input-group"
                          label="Confirm Password"
                          label-for="password-confirm">
                <b-form-input id="password-confirm"
                              type="password"
                              v-model="form.password_confirmation"
                              name="password_confirmation"
                              placeholder="Confirm password">
                </b-form-input>
                <b-form-invalid-feedback
                        :state="!form.errors.has('password_confirmation')"
                        v-text="form.errors.get('password_confirmation')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <b-form-group>
                <b-form-checkbox id="is-otp-verification-enabled-at-login"
                                    v-model="form.is_otp_verification_enabled_at_login"
                                    name="is_otp_verification_enabled_at_login"
                                    @change="onIsOtpVerificationEnabledAtLoginChange"
                                    switch>
                    Is OTP Verification Enabled At Login
                </b-form-checkbox>
                <b-form-invalid-feedback
                        :state="!form.errors.has('is_otp_verification_enabled_at_login')"
                        v-text="form.errors.get('is_otp_verification_enabled_at_login')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <div class="otp-options" v-if="form.is_otp_verification_enabled_at_login">
                <b-form-group id="otp-type-input-group"
                                label="OTP Type"
                                label-for="otp-type">
                    <b-form-select id="otp-type"
                                    v-model="form.otp_type"
                                    name="otp_type"
                                    :options="otpTypeOptions"
                                    @change="onOtpTypeChange"></b-form-select>
                    <b-form-invalid-feedback
                            :state="!form.errors.has('otp_type')"
                            v-text="form.errors.get('otp_type')"
                    ></b-form-invalid-feedback>
                </b-form-group>
                <b-form-group id="pin-input-group"
                              label="PIN"
                              label-for="pin"
                              v-if="form.otp_type === 'pin'">
                    <b-form-input id="pin"
                                  type="password"
                                  v-model="form.pin"
                                  name="pin"
                                  placeholder="Enter PIN">
                    </b-form-input>
                    <b-form-invalid-feedback
                            :state="!form.errors.has('pin')"
                            v-text="form.errors.get('pin')"
                    ></b-form-invalid-feedback>
                </b-form-group>
            </div>
            <b-form-group>
                <b-form-checkbox id="is-client-lock-enabled"
                                 v-model="form.is_client_lock_enabled"
                                 name="is_client_lock_enabled"
                                 @change="onIsClientLockEnabledChange"
                                 switch>
                    Is Client Lock Enabled
                </b-form-checkbox>
                <b-form-invalid-feedback
                        :state="!form.errors.has('is_client_lock_enabled')"
                        v-text="form.errors.get('is_client_lock_enabled')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <b-form-group id="clients-allowed-input-group"
                          class="clients-allowed"
                          label="Clients Allowed"
                          label-for="clients-allowed"
                          v-if="form.is_client_lock_enabled">
                <b-form-input id="clients-allowed"
                              type="number"
                              v-model="form.clients_allowed"
                              name="clients_allowed"
                              placeholder="Enter the number of clients allowed">
                </b-form-input>
                <b-form-invalid-feedback
                        :state="!form.errors.has('clients_allowed')"
                        v-text="form.errors.get('clients_allowed')"
                ></b-form-invalid-feedback>
            </b-form-group>
            <b-form-group>
                <b-form-checkbox id="is-ip-lock-enabled"
                                 v-model="form.is_ip_lock_enabled"
                                 name="is_ip_lock_enabled"
                                 switch>
                    Is IP Lock Enabled
                </b-form-checkbox>
                <b-form-invalid-feedback
                        :state="!form.errors.has('is_ip_lock_enabled')"
                        v-text="form.errors.get('is_ip_lock_enabled')"
                ></b-form-invalid-feedback>
            </b-form-group>
        </b-form>
    </div>
</template>

<script>
  import Form from '@/utils/Form/Form'

  export default {
    data () {
      return {
        form: new Form({
          name: '',
          email: '',
          mobile_number: '',
          password: '',
          password_confirmation: '',
          is_otp_verification_enabled_at_login: false,
          otp_type: 'pin',
          pin: null,
          is_client_lock_enabled: false,
          clients_allowed: 1,
          is_ip_lock_enabled: false
        }),
        otpTypeOptions: [
          { value: 'pin', text: 'PIN' },
          { value: 'mail', text: 'Mail' },
          { value: 'sms', text: 'SMS' },
          { value: 'google2fa', text: 'Google 2FA' }
        ]
      }
    },
    methods: {
      submit() {
        return this.form.post('/api/users')
      },
      onIsOtpVerificationEnabledAtLoginChange(checked) {
        if (! checked) {
          this.form.otp_type = 'pin'
          this.form.pin = null
        }
      },
      onOtpTypeChange(selected) {
        if (selected !== 'pin')
          this.form.pin = null
      },
      onIsClientLockEnabledChange(checked) {
        if (! checked)
          this.form.clients_allowed = 1
      }
    }
  }
</script>