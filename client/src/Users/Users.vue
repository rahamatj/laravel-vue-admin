<template>
    <div>
        <page-title :heading=heading
                    :icon=icon
                    @create-new="$bvModal.show('create-user-modal')">
        </page-title>
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
                    <b-button variant="info"
                              size="sm"
                              class="mr-2">
                        Details
                    </b-button>
                    <b-button variant="primary"
                              size="sm"
                              class="mr-2"
                              @click="editUser(row.item.id)">
                        Edit
                    </b-button>
                </template>
            </datatable>
        </b-card>
        <b-modal id="create-user-modal" title="Create User" size="lg">
            <create-user ref="createUser"></create-user>
            <template v-slot:modal-footer>
                <div class="w-100">
                    <div class="float-right">
                        <b-button
                                variant="danger"
                                class="mr-2"
                                @click="$bvModal.hide('create-user-modal')"
                                :disabled="isCreating"
                        >
                            Cancel
                        </b-button>
                        <b-button
                                variant="success"
                                @click="storeUser"
                                :disabled="isStoring"
                        ><b-spinner class="spinner"
                                    small
                                    v-if="isStoring"
                        ></b-spinner>
                            Ok
                        </b-button>
                    </div>
                </div>
            </template>
        </b-modal>
        <b-modal id="edit-user-modal" title="Edit User" size="lg">
            <edit-user ref="editUser" :id="editingUserId"></edit-user>
            <template v-slot:modal-footer>
                <div class="w-100">
                    <div class="float-right">
                        <b-button
                                variant="danger"
                                class="mr-2"
                                @click="$bvModal.hide('edit-user-modal')"
                                :disabled="isUpdating"
                        >
                            Cancel
                        </b-button>
                        <b-button
                                variant="success"
                                @click="updateUser"
                                :disabled="isUpdating"
                        ><b-spinner class="spinner"
                                    small
                                    v-if="isUpdating"
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
  import EditUser from '@/Users/EditUser'

  export default {
    components: {
      PageTitle,
      CreateUser,
      EditUser
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
      isStoring: false,
      editingUserId: 0,
      isUpdating: false
    }),
    methods: {
      storeUser() {
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
      },
      editUser(id) {
        this.editingUserId = id
        this.$bvModal.show('edit-user-modal')
      },
      updateUser() {
        this.isUpdating = true;

        this.$refs.editUser.submit()
            .then(data => {
              this.successMessage = data.message
              this.isUpdating = false
              this.$bvModal.hide('edit-user-modal')
              this.$refs.usersTable.refresh()
            })
            .catch(data => {
              this.isUpdating = false
              console.error(data.message)
            })
      }
    }
  }
</script>
