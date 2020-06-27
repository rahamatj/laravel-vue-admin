<template>
    <div>
        <div class="h-100 bg-plum-plate bg-animation">
            <div class="d-flex h-100 justify-content-center align-items-center">
                <b-col md="8" class="mx-auto app-login-box">
                    <b-alert :show="form.hasSuccessMessage()"
                             variant="success"
                             dismissible
                    >
                        {{ form.getSuccessMessage() }}
                    </b-alert>
                    <b-alert :show="resendMessage !== ''"
                             variant="success"
                             dismissible
                    >
                        {{ resendMessage }}
                    </b-alert>
                    <b-alert :show="form.errors.hasMessage()"
                             variant="danger"
                             dismissible
                    >
                        {{ form.errors.getMessage() }}
                    </b-alert>
                    <div class="modal-dialog w-100 mx-auto">
                        <b-form id="checkpoint-form"
                                @submit.prevent="check"
                                @keydown="form.errors.clear()"
                        >
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="h5 modal-title text-center">
                                        <h4 class="mt-2">
                                            <div>{{ app.name }}</div>
                                            <span>{{ otp.prompt(user.otp_type) }}</span><br>
                                            <span>Please verify your OTP below.</span>
                                        </h4>
                                    </div>
                                    <b-form-group id="otpInputGroup"
                                                  label-for="otp">
                                        <b-form-input id="otp"
                                                      type="text"
                                                      name="otp"
                                                      placeholder="Enter OTP..."
                                                      v-model="form.otp"
                                        >
                                        </b-form-input>
                                        <b-form-invalid-feedback
                                                :state="!form.errors.has('otp')"
                                                v-text="form.errors.get('otp')"
                                        ></b-form-invalid-feedback>
                                    </b-form-group>
                                    <div class="divider"/>
                                </div>
                                <div class="modal-footer clearfix">
                                    <div class="float-left">
                                        <button id="logout" @click="logout" class="btn-lg btn btn-link">Logout</button>
                                    </div>
                                    <div class="float-left" v-if="otp.resendable(user.otp_type)">
                                        <b-button id="resend"
                                                  type="button"
                                                  variant="primary"
                                                  size="lg"
                                                  @click="resend"
                                                  :disabled="isResending"
                                        ><b-spinner class="resend-spinner"
                                                    small
                                                    v-if="isResending"
                                        ></b-spinner>
                                            Resend
                                        </b-button>
                                    </div>
                                    <div class="float-right">
                                        <b-button id="verify"
                                                  type="submit"
                                                  variant="primary"
                                                  size="lg"
                                                  :disabled="form.errors.any() || form.isLoading()"
                                        ><b-spinner class="spinner"
                                                    small
                                                    v-if="form.isLoading()"
                                        ></b-spinner>
                                            Verify
                                        </b-button>
                                    </div>
                                </div>
                            </div>
                        </b-form>
                    </div>
                    <div class="text-center text-white opacity-8 mt-3">
                        Copyright &copy; {{ app.companyName }} {{ year }}
                    </div>
                </b-col>
            </div>
        </div>
    </div>
</template>

<script>
  import Form from '@/utils/Form'
  import otp from '@/utils/otp'
  import { mapGetters, mapActions, mapMutations } from 'vuex'

  export default {
    data() {
      return {
        app: app,
        otp: otp,
        isResending: false,
        resendMessage: '',
        year: (new Date()).getFullYear(),
        form: new Form({
          otp: ''
        })
      }
    },
    computed: {
      ...mapGetters('login', ['user'])
    },
    methods: {
      ...mapActions('login', ['unauthenticate']),
      ...mapActions('checkpoint', ['verify']),
      ...mapMutations('login', ['SET_IS_LOGGING_OUT']),
      check() {
        this.verify(this.form)
            .then(() => {
              this.$router.replace({ name: 'dashboard' })
            })
      },
      resend() {
        this.isResending = true
        axios.get('/api/checkpoint/resend')
            .then(response => {
              this.resendMessage = response.data.message
              this.isResending = false
            })
      },
      logout() {
        this.SET_IS_LOGGING_OUT(true)
        this.unauthenticate()
            .then(() => {
              this.SET_IS_LOGGING_OUT(false)
              this.$router.replace({ name: 'login' })
            })
      }
    }
  }
</script>
