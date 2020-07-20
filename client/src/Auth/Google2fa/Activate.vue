<template>
    <div>
        <div class="h-100 bg-plum-plate bg-animation">
            <div class="d-flex h-100 justify-content-center align-items-center">
                <b-col md="8" class="mx-auto app-login-box">
                    <div class="modal-dialog w-100 mx-auto">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="h5 modal-title text-center">
                                    <h4 class="mt-2">
                                        <div>{{ app.name }}</div>
                                        <span>Please scan the QR code below with Google Authenticator app.</span><br>
                                        <span>This page will only appear once.</span><br>
                                        <span>Once you click Ok you will never be able come back here.</span>
                                    </h4>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="position-relative text-center">
                                            <b-spinner class="spinner"
                                                       variant="primary"
                                                       v-if="isGettingG2faUrl"
                                            ></b-spinner>
                                            <img class="qrcode" :src="g2faUrl" v-if="!isGettingG2faUrl">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer clearfix">
                                <div class="float-right">
                                    <b-button id="ok"
                                              type="submit"
                                              variant="primary"
                                              size="lg"
                                              @click="redirectToCheckpoint"
                                              :disabled="isGettingG2faUrl"
                                    >
                                        Ok
                                    </b-button>
                                </div>
                            </div>
                        </div>
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
  import { mapGetters, mapMutations } from 'vuex'

  export default {
    data() {
      return {
        app: app,
        year: (new Date()).getFullYear(),
        isGettingG2faUrl: false,
        g2faUrl: ''
      }
    },
    computed: {
      ...mapGetters('login', ['user'])
    },
    methods: {
      ...mapMutations('login', ['SET_USER']),
      getG2faUrl() {
        this.isGettingG2faUrl = true
        axios.get('/api/checkpoint/google2fa/activate')
            .then(response => {
              this.g2faUrl = response.data.g2faUrl
              this.isGettingG2faUrl = false
            })
            .catch(error => {
              console.error(error.response.data.message)
              this.isGettingG2faUrl = false
            })
      },
      redirectToCheckpoint() {
        this.user.is_google2fa_activated = true
        this.SET_USER(this.user)

        this.$router.replace({ name: 'checkpoint' })
      }
    },
    created() {
      this.getG2faUrl()
    }
  }
</script>
