<template>
    <div>
        <div class="d-flex h-100 justify-content-center align-items-center" v-if="isLoading">
            <b-spinner class="spinner" variant="primary"></b-spinner>
        </div>
        <div v-if="!isLoading">
            <b-table stacked
                     :items="users"
                     :fields="fields">
            </b-table>
        </div>
    </div>
</template>

<script>

  export default {
    props: {
      id: {
        type: Number,
        required: true
      }
    },
    data() {
      return {
        users: [],
        fields: [
          'name',
          'email',
          'mobile_number',
          {
            key: 'is_otp_verification_enabled_at_login',
            label: 'OTP at Login',
            formatter: value => value ? 'Yes' : 'No'
          },
          {
            key: 'otp_type',
            formatter: (value, key, item) => {
              if (item.is_otp_verification_enabled_at_login) {
                switch (value) {
                  case 'pin':
                    return 'PIN'
                  case 'mail':
                    return 'Mail'
                  case 'sms':
                    return 'SMS'
                  case 'google2fa':
                    return 'Google 2FA'
                }
              } else {
                return 'N/A'
              }
            }
          },
          {
            key: 'is_client_lock_enabled',
            formatter: value => value ? 'Yes' : 'No'
          },
          {
            key: 'clients_allowed',
            formatter: (value, key, item) => item.is_client_lock_enabled ? value : 'N/A'
          },
          {
            key: 'is_ip_lock_enabled',
            formatter: value => value ? 'Yes' : 'No'
          }
        ],
        isLoading: false
      }
    },
    methods: {
      getUser() {
        this.isLoading = true

        axios.get('/api/users/' + this.id)
            .then(response => {
              this.users.push(response.data.data)

              this.isLoading = false
            })
            .catch(error => {
              console.error(error.response.data.message)
              this.isLoading = false
            })
      }
    },
    created() {
      this.getUser()
    }
  }
</script>