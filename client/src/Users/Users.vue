<template>
    <div>
        <page-title :heading=heading :icon=icon></page-title>
        <b-card class="main-card mb-4">
            <datatable apiUrl="/api/users"
                        :fields="fields">
                <template v-slot:cell(actions)="row">
                    <b-button size="sm" class="mr-2">
                        Details
                    </b-button>
                </template>
            </datatable>
        </b-card>
    </div>
</template>

<script>
  import PageTitle from "@/Layout/Components/PageTitle.vue";

  export default {
    components: {
      PageTitle,
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
    }),
    methods: {

    }
  }
</script>
