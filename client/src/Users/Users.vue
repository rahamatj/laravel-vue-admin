<template>
  <div>
    <page-title :heading=heading
                :icon=icon
                @button-click="$bvModal.show('create-user-modal')">
    </page-title>
    <b-alert :show="successMessage !== ''"
             variant="success"
             dismissible
    >
      {{ successMessage }}
    </b-alert>
    <b-card class="main-card mb-4">
      <datatable ref="usersTable"
                 api-url="/api/users"
                 :fields="fields">
        <template v-slot:cell(actions)="row">
          <b-button variant="info"
                    size="sm"
                    class="mr-2 mb-2"
                    @click="show(row.item.id)">
            Details
          </b-button>
          <b-button variant="primary"
                    size="sm"
                    class="mr-2 mb-2"
                    @click="edit(row.item.id)">
            Edit
          </b-button>
          <b-button variant="danger"
                    size="sm"
                    class="mr-2 mb-2"
                    @click="destroy(row.item.id)">
            Delete
          </b-button>
        </template>
      </datatable>
    </b-card>
    <b-modal id="show-user-modal" title="User Details" size="lg">
      <show-user ref="showUser" :id="id"></show-user>
      <template v-slot:modal-footer>
        <div class="w-100">
          <div class="float-right">
            <b-button
                variant="danger"
                class="mr-2"
                @click="$bvModal.hide('show-user-modal')"
            >
              Cancel
            </b-button>
            <b-button
                variant="success"
                @click="$bvModal.hide('show-user-modal')"
            >
              Ok
            </b-button>
          </div>
        </div>
      </template>
    </b-modal>
    <b-modal id="create-user-modal" title="Create User" size="lg">
      <create-user ref="createUser"></create-user>
      <template v-slot:modal-footer>
        <div class="w-100">
          <div class="float-right">
            <b-button
                variant="danger"
                class="mr-2"
                @click="$bvModal.hide('create-user-modal')"
                :disabled="isStoring"
            >
              Cancel
            </b-button>
            <b-button
                variant="success"
                @click="store"
                :disabled="isStoring"
            >
              <b-spinner class="spinner"
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
      <edit-user ref="editUser" :id="id"></edit-user>
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
                @click="update"
                :disabled="isUpdating"
            >
              <b-spinner class="spinner"
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
  import ShowUser from '@/Users/ShowUser'
  import convertToLocaleDateTimeString from '@/utils/convertToLocaleDateTimeString'

  export default {
    components: {
      PageTitle,
      CreateUser,
      EditUser,
      ShowUser
    },
    data: () => ({
      heading: 'Users',
      icon: 'pe-7s-users icon-gradient bg-happy-itmeo',
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
          formatter: value => value ? 'Yes' : 'No'
        },
        {
          key: 'otp_type',
          sortable: true
        },
        {
          key: 'is_active',
          label: 'Activity Status',
          formatter: value => value ? 'Active' : 'Inactive',
          sortable: true
        },
        {
          key: 'last_logged_in_at',
          formatter: value => convertToLocaleDateTimeString(value),
          sortable: true
        },
        {
          key: 'created_at',
          formatter: value => convertToLocaleDateTimeString(value),
          sortable: true
        },
        'actions'
      ],
      successMessage: '',
      isStoring: false,
      id: 0,
      isUpdating: false
    }),
    methods: {
      store() {
        this.isStoring = true;

        this.$refs.createUser.submit()
            .then(data => {
              this.successMessage = data.message
              this.isStoring = false
              this.$bvModal.hide('create-user-modal')
              this.$refs.usersTable.refresh()
            })
            .catch(data => {
              this.isStoring = false
              console.error(data.message)
            })
      },
      show(id) {
        this.id = id
        this.$bvModal.show('show-user-modal')
      },
      edit(id) {
        this.id = id
        this.$bvModal.show('edit-user-modal')
      },
      update() {
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
      },
      destroy(id) {
        this.$bvModal.msgBoxConfirm('Are you sure you want to delete this?', {
          title: 'Please Confirm',
          okVariant: 'danger',
          okTitle: 'YES',
          cancelTitle: 'NO',
        })
            .then(value => {
              if (value) {
                axios.delete('/api/users/' + id)
                    .then(response => {
                      this.successMessage = response.data.message
                      this.$refs.usersTable.refresh()
                    })
                    .catch(error => console.error(error.response.data.message))
              }
            })
            .catch(error => console.error(error.response.data.message))
      }
    }
  }
</script>
