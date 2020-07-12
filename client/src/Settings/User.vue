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
        <edit-user ref="editUser" :id="id"></edit-user>
        <div>
            <b-button id="submit"
                      type="button"
                      variant="primary"
                      size="lg"
                      @click="update"
                      :disabled="isUpdating"
            >
                <b-spinner class="spinner"
                           small
                           v-if="isUpdating"
                ></b-spinner>
                Submit
            </b-button>
        </div>
    </div>
</template>

<script>
  import PageTitle from '@/Layout/Components/PageTitle.vue'
  import EditUser from '@/Users/EditUser'
  import {mapGetters, mapMutations} from 'vuex'

  export default {
    components: {
      PageTitle,
      EditUser
    },
    data() {
      return {
        heading: 'User Settings',
        icon: 'pe-7s-user icon-gradient bg-happy-itmeo',
        isUpdating: false,
        successMessage: '',
        id: 0
      }
    },
    computed: {
      ...mapGetters('login', ['user'])
    },
    methods: {
      ...mapMutations('login', ['SET_USER']),
      update() {
        this.isUpdating = true;

        this.$refs.editUser.submit()
            .then(data => {
              this.SET_USER(data.data)
              this.$refs.editUser.getUser()
              this.successMessage = data.message
              this.isUpdating = false
            })
            .catch(data => {
              this.isUpdating = false
              console.error(data.message)
            })
      },
      getId() {
        this.id = this.user.id
      },

    },
    created() {
      this.getId()
    }
  }
</script>