<template>
    <div>
        <b-row>
            <b-col sm="3" md="2" lg="2" class="my-1">
                <b-form-group
                        label="Per page"
                        label-cols-sm="6"
                        label-cols-md="6"
                        label-cols-lg="6"
                        label-align-sm="left"
                        label-size="sm"
                        label-for="perPageSelect"
                        class="mb-0"
                >
                    <b-form-select
                            v-model="perPage"
                            id="perPageSelect"
                            size="sm"
                            :options="pageOptions"
                    ></b-form-select>
                </b-form-group>
            </b-col>

            <b-col sm="6" md="6" lg="4" offset-sm="3" offset-md="4" offset-lg="6" class="my-1">
                <b-form-group
                        label="Filter"
                        label-cols-sm="3"
                        label-align-sm="right"
                        label-size="sm"
                        label-for="filterInput"
                        class="mb-0"
                >
                    <b-input-group size="sm">
                        <b-form-input
                                v-model="filter"
                                debounce="200"
                                type="search"
                                id="filterInput"
                                placeholder="Type to Search"
                        ></b-form-input>
                        <b-input-group-append>
                            <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
        </b-row>

        <b-table :items="items"
                 :fields="fields"
                 :filter="filter"
                 :per-page="perPage"
                 :current-page="currentPage"
                 responsive="md"
                 stacked="sm"
                 :api-url="apiUrl"
                 show-empty
                 sort-icon-left
                 no-local-sorting>
            <template v-slot:table-busy>
                <div class="text-center text-primary my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>

            <slot v-for="(_, name) in $slots" :name="name" :slot="name"/>

            <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
                <slot :name="name" v-bind="slotData"/>
            </template>

        </b-table>

        <b-pagination
                v-model="currentPage"
                :total-rows="totalRows"
                :per-page="perPage"
                align="left"
                size="sm"
                class="my-0"
        ></b-pagination>
    </div>
</template>

<script>
  export default {
    props: {
      apiUrl: {
        type: String,
        required: true
      },
      fields: {
        type: Array,
        required: true
      }
    },
    data() {
      return {
        filter: null,
        perPage: 25,
        pageOptions: [25, 50, 100],
        totalRows: 1,
        currentPage: 1
      }
    },
    methods: {
      items(ctx) {
        let params = ''

        params += '?per_page=' + ctx.perPage
        params += '&page=' + ctx.currentPage

        if (ctx.sortBy) {
          params += '&sort_by=' + ctx.sortBy

          if (ctx.sortDesc)
            params += '&sort_desc=1'
        }

        if (ctx.filter)
          params += '&filter=' + ctx.filter

        return axios.get(ctx.apiUrl + params)
            .then(response => {
              this.totalRows = response.data.meta.total

              return response.data.data
            })
            .catch(error => {
              console.error(error.response.data.message)

              return []
            })
      }
    }
  }
</script>