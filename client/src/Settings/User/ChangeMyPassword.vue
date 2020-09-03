<template>
    <div>
        <b-form @keydown="form.errors.clear()">
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
        </b-form>
    </div>
</template>

<script>
  import Form from '@/utils/Form'

  export default {
    props: {
      id: {
        type: Number,
        required: true
      }
    },
    data() {
      return {
        form: new Form({
          password: '',
          password_confirmation: ''
        })
      }
    },
    methods: {
      submit() {
        return this.form.patch('/api/settings/user/password')
      }
    }
  }
</script>