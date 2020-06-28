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
                    <b-alert :show="form.errors.hasMessage()"
                             variant="danger"
                             dismissible
                    >
                        {{ form.errors.getMessage() }}
                    </b-alert>
                    <div class="modal-dialog w-100 mx-auto">
                        <b-form id="reset-password-form"
                                @submit.prevent="reset"
                                @keydown="form.errors.clear()"
                        >
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="h5 modal-title text-center">
                                        <h4 class="mt-2">
                                            <div>{{ app.name }}</div>
                                            <span>Please update your password.</span>
                                        </h4>
                                    </div>
                                    <b-form-group id="emailInputGroup"
                                                  label-for="email">
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
                                    <b-form-group id="passwordConfirmationInputGroup"
                                                  label-for="password-confirm">
                                        <b-form-input id="password-confirm"
                                                      type="password"
                                                      name="password_confirmation"
                                                      placeholder="Confirm password..."
                                                      v-model="form.password_confirmation"
                                        >
                                        </b-form-input>
                                    </b-form-group>
                                    <div class="divider"/>
                                </div>
                                <div class="modal-footer clearfix">
                                    <div class="float-left" v-if="passwordReset">
                                        <router-link :to="{ name: 'login' }" class="btn-lg btn btn-link">Login</router-link>
                                    </div>
                                    <div class="float-right">
                                        <b-button id="reset-password"
                                                  type="submit"
                                                  variant="primary"
                                                  size="lg"
                                                  :disabled="form.errors.any() || form.isLoading()"
                                        ><b-spinner class="spinner"
                                                    small
                                                    v-if="form.isLoading()"
                                        ></b-spinner>
                                            Reset Password
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
  import Form from '@/utils/Form/Form'

  export default {
    props: ['token'],
    data() {
      return {
        app: app,
        year: (new Date()).getFullYear(),
        passwordReset: false,
        form: new Form({
          email: '',
          password: '',
          password_confirmation: '',
          token: this.token
        })
      }
    },
    methods: {
      reset() {
        this.form.post('/api/password/reset')
            .then(data => this.passwordReset = true)
            .catch(data => console.log(data))
      }
    }
  }
</script>
