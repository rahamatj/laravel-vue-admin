<template>
    <div>
        <page-title :heading=heading :icon=icon @create-new="onCreateNew"></page-title>
        <b-alert :show="successMessage !== ''"
                 variant="success"
                 dismissible
        >
            {{ successMessage }}
        </b-alert>
        <b-card class="main-card mb-4">
            <datatable ref="usersTable"
                       apiUrl="/api/users"
                       :fields="fields">
                <template v-slot:cell(actions)="row">
                    <b-button size="sm" class="mr-2">
                        Details
                    </b-button>
                </template>
            </datatable>
        </b-card>
        <b-modal id="create-user-modal" title="Create User" size="lg" @ok="createUser">
            <create-user ref="createUser"></create-user>
            <template v-slot:modal-footer>
                <div class="w-100">
                    <div class="float-right">
                        <b-button
                                variant="danger"
                                class="mr-1"
                                @click="$bvModal.hide('create-user-modal')"
                                :disabled="isCreating"
                        >
                            Cancel
                        </b-button>
                        <b-button
                                variant="success"
                                @click="createUser"
                                :disabled="isCreating"
                        ><b-spinner class="spinner"
                                    small
                                    v-if="isCreating"
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
  import CreateUser from '@/Users/CreateUser'

  export default {
    components: {
      PageTitle,
      CreateUser
    },
    data: () => ({
      heading: 'Users',
      icon: 'pe-7s-add-user icon-gradient bg-happy-itmeo',
      fields: [
        {
          key: 'row_no',
          label: '#'
        },
        {
          key: 'name',
          sortable: true
        },
        {
          key: 'email',
          sortable: true
        },
        {
          key: 'mobile_number',
          sortable: true
        },
        {
          key: 'is_otp_verification_enabled_at_login',
          label: 'OTP at Login',
          formatter: value => {
            return value ? 'Yes' : 'No'
          }
        },
        {
          key: 'otp_type',
          sortable: true
        },
        'actions'
      ],
      successMessage: '',
      isCreating: false
    }),
    methods: {
      onCreateNew() {
        this.$bvModal.show('create-user-modal')
      },
      createUser() {
        this.isCreating = true;

        this.$refs.createUser.submit()
            .then(data => {
              this.successMessage = data.message
              this.isCreating = false
              this.$bvModal.hide('create-user-modal')
              this.$refs.usersTable.refresh()
            })
            .catch(data => {
              this.isCreating = false
              console.error(data.message)
            })
      }
    }
  }
</script>
