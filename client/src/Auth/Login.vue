<template>
    <div>
        <div class="h-100 bg-plum-plate bg-animation">
            <div class="d-flex h-100 justify-content-center align-items-center">
                <b-col md="8" class="mx-auto app-login-box">
                    <b-alert :show="form.errors.hasMessage()"
                             variant="danger"
                             dismissible
                             v-text="form.errors.getMessage()"
                    >
                    </b-alert>
                    <div class="modal-dialog w-100 mx-auto">
                        <b-form id="login-form"
                                @submit.prevent="login"
                                @keydown="form.errors.clear($event.target.name)"
                        >
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="h5 modal-title text-center">
                                        <h4 class="mt-2">
                                            <div>Eload</div>
                                            <span>Please sign in to your account below.</span>
                                        </h4>
                                    </div>
                                    <b-form-group id="emailInputGroup"
                                                  label-for="email"
                                                  description="We'll never share your email with anyone else.">
                                        <b-form-input id="email"
                                                      type="email"
                                                      name="email"
                                                      placeholder="Enter email..."
                                                      v-model="form.email"
                                        >
                                        </b-form-input>
                                        <b-form-invalid-feedback
                                                :state="!form.errors.has('email')"
                                                v-text="form.errors.get('email')"
                                        ></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="passwordInputGroup"
                                                  label-for="password">
                                        <b-form-input id="password"
                                                      type="password"
                                                      name="password"
                                                      placeholder="Enter password..."
                                                      v-model="form.password"
                                        >
                                        </b-form-input>
                                        <b-form-invalid-feedback
                                                :state="!form.errors.has('password')"
                                                v-text="form.errors.get('password')"
                                        ></b-form-invalid-feedback>
                                    </b-form-group>
                                    <div class="divider"/>
                                </div>
                                <div class="modal-footer clearfix">
                                    <div class="float-left">
                                        <a href="javascript:void(0);" class="btn-lg btn btn-link">Recover
                                            Password</a>
                                    </div>
                                    <div class="float-right">
                                        <b-button id="login"
                                                  type="submit"
                                                  variant="primary"
                                                  size="lg"
                                                  :disabled="form.errors.any()"
                                        >Login to Dashboard
                                        </b-button>
                                    </div>
                                </div>
                            </div>
                        </b-form>
                    </div>
                    <div class="text-center text-white opacity-8 mt-3">
                        Copyright &copy; Hosting4bd Ltd. {{ year }}
                    </div>
                </b-col>
            </div>
        </div>
    </div>
</template>

<script>
  import Form from '../utils/Form'
  import {mapActions} from 'vuex'
  import Fingerprint2 from 'fingerprintjs2'

  export default {
    data() {
      return {
        year: '',
        form: new Form({
          email: '',
          password: '',
          fingerprint: '',
          client: '',
          platform: ''
        })
      }
    },
    methods: {
      ...mapActions('login', ['getToken']),
      login() {
        this.getToken(this.form)
            .then(data => this.$router.replace({ name: 'dashboard' }))
            .catch(data => console.log(data))
      },
      getClientInfo() {
        Fingerprint2.get({
          excludes: {
            hardwareConcurrency: true,
            canvas: true,
            webgl: true,
            adBlock: true,
            fonts: true,
            audio: true,
            enumerateDevices: true
          }
        }, components => {
          for (let component of components) {
            if (component.key === 'userAgent')
              this.form.client = component.value;
            if (component.key === 'platform')
              this.form.platform = component.value;
          }

          const values = components.map(function (component) { return component.value });
          this.form.fingerprint = Fingerprint2.x64hash128(values.join(''), 31);
        })
      }
    },
    created() {
      this.year = (new Date()).getFullYear()
      this.getClientInfo()
    }
  }
</script>
