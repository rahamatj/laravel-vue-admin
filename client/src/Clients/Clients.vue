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
        <b-card class="main-card mb-4">
            <datatable ref="clientsTable"
                       apiUrl="/api/clients"
                       :fields="fields">
                <template v-slot:cell(actions)="row">
                    <b-button variant="danger"
                              size="sm"
                              class="mr-2 mb-2"
                              @click="changeEnabledStatus(row.item.id)"
                              v-if="row.item.is_enabled">
                        Disable
                    </b-button>
                    <b-button variant="primary"
                              size="sm"
                              class="mr-2 mb-2"
                              @click="changeEnabledStatus(row.item.id)"
                              v-if="!row.item.is_enabled">
                        Enable
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
    </div>
</template>

<script>
  import PageTitle from '@/Layout/Components/PageTitle.vue'
  import convertToLocaleDateTimeString from '@/utils/convertToLocaleDateTimeString'

  export default {
    components: {
      PageTitle
    },
    data: () => ({
      heading: 'Clients',
      icon: 'pe-7s-phone icon-gradient bg-happy-itmeo',
      fields: [
        {
          key: 'row_no',
          label: '#'
        },
        {
          key: 'user_name',
          sortable: true
        },
        {
          key: 'client',
          sortable: true
        },
        {
          key: 'platform',
          sortable: true
        },
        {
          key: 'ip',
          sortable: true
        },
        {
          key: 'is_enabled',
          formatter: value => value ? 'Yes' : 'No'
        },
        {
          key: 'logged_in_at',
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
    }),
    methods: {
      changeEnabledStatus(id) {
        axios.patch('/api/clients/' + id + '/enabled')
            .then(response => {
              this.successMessage = response.data.message
              this.$refs.clientsTable.refresh()
            })
            .catch(error => console.error(error.response.message))
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
                axios.delete('/api/clients/' + id)
                    .then(response => {
                      this.successMessage = response.data.message
                      this.$refs.clientsTable.refresh()
                    })
                    .catch(error => console.error(error.response.data.message))
              }
            })
            .catch(error => console.error(error.response.data.message))
      }
    }
  }
</script>
