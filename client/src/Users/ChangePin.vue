<template>
    <div>
        <b-form @keydown="form.errors.clear()">
            <b-form-group id="pin-input-group"
                          label="PIN"
                          label-for="pin">
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
        </b-form>
    </div>
</template>

<script>
  import Form from '@/utils/Form/Form'

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
          pin: ''
        })
      }
    },
    methods: {
      submit() {
        return this.form.patch('/api/users/' + this.id + '/pin')
      }
    }
  }
</script>