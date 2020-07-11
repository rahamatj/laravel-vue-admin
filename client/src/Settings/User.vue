<template>
    <div>
        <page-title :heading=heading
                    :icon=icon
                    no-create-new>
        </page-title>
        <b-alert :show="successMessage !== ''"
                 variant="success"
                 dismissible
        >
            {{ successMessage }}
        </b-alert>
        <b-alert :show="form.hasSuccessMessage()"
                 variant="success"
                 dismissible
        >
            {{ form.getSuccessMessage() }}
        </b-alert>
        <b-alert :show="form.errors.hasMessage()"
                 variant="danger"
                 dismissible
        >
            {{ form.errors.getMessage() }}
        </b-alert>
        <b-form @keydown="form.errors.clear($event.target.name)"
                @change="form.errors.clear($event.target.name)"
                @submit.prevent="submit">
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
                <b-button id="password"
                          variant="primary"
                          @click="$bvModal.show('change-password-modal')"
                >
                    Change Password
                </b-button>
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
                    <b-button id="pin"
                              variant="primary"
                              @click="$bvModal.show('change-pin-modal')"
                    >
                        Change PIN
                    </b-button>
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
            <div>
                <b-button id="submit"
                          type="submit"
                          variant="primary"
                          size="lg"
                          :disabled="form.errors.any() || form.isLoading()"
                >
                    <b-spinner class="spinner"
                               small
                               v-if="form.isLoading()"
                    ></b-spinner>
                    Submit
                </b-button>
            </div>
        </b-form>
        <b-modal id="change-password-modal" title="Change Password" size="lg">
            <change-password ref="changePassword" :id="id"></change-password>
            <template v-slot:modal-footer>
                <div class="w-100">
                    <div class="float-right">
                        <b-button
                                variant="danger"
                                class="mr-2"
                                @click="$bvModal.hide('change-password-modal')"
                                :disabled="isUpdatingPassword"
                        >
                            Cancel
                        </b-button>
                        <b-button
                                variant="success"
                                @click="updatePassword"
                                :disabled="isUpdatingPassword"
                        >
                            <b-spinner class="spinner"
                                       small
                                       v-if="isUpdatingPassword"
                            ></b-spinner>
                            Ok
                        </b-button>
                    </div>
                </div>
            </template>
        </b-modal>
        <b-modal id="change-pin-modal" title="Change PIN" size="lg">
            <change-pin ref="changePin" :id="id"></change-pin>
            <template v-slot:modal-footer>
                <div class="w-100">
                    <div class="float-right">
                        <b-button
                                variant="danger"
                                class="mr-2"
                                @click="$bvModal.hide('change-pin-modal')"
                                :disabled="isUpdatingPin"
                        >
                            Cancel
                        </b-button>
                        <b-button
                                variant="success"
                                @click="updatePin"
                                :disabled="isUpdatingPin"
                        >
                            <b-spinner class="spinner"
                                       small
                                       v-if="isUpdatingPin"
                            ></b-spinner>
                            Ok
                        </b-button>
                    </div>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
  import PageTitle from '@/Layout/Components/PageTitle.vue'
  import Form from '@/utils/Form/Form'
  import ChangePassword from '@/Users/ChangePassword'
  import ChangePin from '@/Users/ChangePin'
  import {mapGetters, mapMutations} from 'vuex'

  export default {
    components: {
      PageTitle,
      ChangePassword,
      ChangePin
    },
    data() {
      return {
        heading: 'User Settings',
        icon: 'pe-7s-user icon-gradient bg-happy-itmeo',
        form: new Form({
          name: '',
          email: '',
          mobile_number: '',
          is_otp_verification_enabled_at_login: false,
          otp_type: 'pin',
          is_client_lock_enabled: false,
          clients_allowed: 1,
          is_ip_lock_enabled: false
        }),
        otpTypeOptions: [
          {value: 'pin', text: 'PIN'},
          {value: 'mail', text: 'Mail'},
          {value: 'sms', text: 'SMS'},
          {value: 'google2fa', text: 'Google 2FA'}
        ],
        successMessage: '',
        isUpdatingPassword: false,
        isUpdatingPin: false,
        id: 0
      }
    },
    computed: {
      ...mapGetters('login', ['user'])
    },
    methods: {
      ...mapMutations('login', ['SET_USER']),
      submit() {
        this.form.patch('/api/users/' + this.id)
            .then(data => {
              this.SET_USER(data.data)
              this.getUser()
            })
            .catch(data => console.error(data.message))
      },
      onIsOtpVerificationEnabledAtLoginChange(checked) {
        if (!checked) {
          this.form.otp_type = 'pin'
          this.form.pin = null
        }
      },
      onOtpTypeChange(selected) {
        if (selected !== 'pin')
          this.form.pin = null
      },
      onIsClientLockEnabledChange(checked) {
        if (!checked)
          this.form.clients_allowed = 1
      },
      getUser() {
        this.id = this.user.id
        this.form.name = this.user.name
        this.form.email = this.user.email
        this.form.mobile_number = this.user.mobile_number
        this.form.is_otp_verification_enabled_at_login = !!this.user.is_otp_verification_enabled_at_login
        this.form.otp_type = this.user.otp_type
        this.form.is_client_lock_enabled = !!this.user.is_client_lock_enabled
        this.form.clients_allowed = this.user.clients_allowed
        this.form.is_ip_lock_enabled = !!this.user.is_ip_lock_enabled
      },
      updatePassword() {
        this.isUpdatingPassword = true

        this.$refs.changePassword.submit()
            .then(data => {
              this.successMessage = data.message
              this.isUpdatingPassword = false
              this.$bvModal.hide('change-password-modal')
            })
            .catch(data => {
              console.error(data.message)
              this.isUpdatingPassword = false
            })
      },
      updatePin() {
        this.isUpdatingPin = true

        this.$refs.changePin.submit()
            .then(data => {
              this.successMessage = data.message
              this.isUpdatingPin = false
              this.$bvModal.hide('change-pin-modal')
            })
            .catch(data => {
              console.error(data.message)
              this.isUpdatingPin = false
            })
      }
    },
    created() {
      this.getUser()
    }
  }
</script>