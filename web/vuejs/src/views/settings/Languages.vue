<template>
  <div>
    <page-title :heading=heading :subheading=subheading :icon=icon :starShow=starShow :buttonActionHide=true></page-title>
    <b-card title="Languages" class="main-card mb-4">
      <b-table :striped="true"
               :bordered="true"
               :outlined="true"
               :small="true"
               :hover="false"
               :dark="false"
               :fixed="false"
               :foot-clone="true"

               :current-page="currentPage"
               :per-page="perPage"
               :filter="filter"
               :sort-by.sync="sortBy"
               :sort-desc.sync="sortDesc"
               :sort-direction="sortDirection"

               @filtered="onFiltered"

               :items="items"
               :fields="fields">

        <template slot="flag" slot-scope="flag">
          <flag :country-acronym="flag.value"></flag>
        </template>
      </b-table>

      <b-row>
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>
      </b-row>

    </b-card>
  </div>
</template>

<script>

  import PageTitle from "../../Layout/Components/PageTitle.vue";
  import flag from "../components/flag";

  import qs from "qs";
  import axios from "axios";

  export default {
    components: {
      PageTitle,
      flag,
    },
    data: () => ({
      heading: 'Languages',
      subheading: 'Languages actions',
      icon: 'pe-7s-add-user icon-gradient bg-happy-itmeo',
      starShow: false,
      buttonActionHide: true,
      // buttonAction: 'language:create',
      totalRows: 0,
      perPage: 25,
      currentPage: 1,

      sortBy: null,
      sortDesc: false,
      sortDirection: 'asc',
      filter: null,

      fields: [
        { key: 'id', sortable: true},
        { key: 'name', sortable: true},
        { key: 'acronim', sortable: true},
        { key: 'flag'},
      ],
      items: [],
    }),
    created: function() {
      this.getLanguages();
    },
    methods: {
      getLanguages: function () {
          axios.get(window.apiDomainUrl+'/languages/get-all-languages', qs.stringify({}))
            .then((response) => {
              this.items = response.data.items;
              this.totalRows = response.data.items.length;
          });
      },
      onFiltered (filteredItems) {
        this.totalRows = filteredItems.length;
        this.currentPage = 1;
      }
    }
  }
</script>
