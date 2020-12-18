<template>
  <div>
    <b-card :title="$store.state.t('Project History')" class="main-card mb-4">
      <b-row class="mb-3">
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>
        <b-col md="6" class="my-1" style="text-align: right; color: grey">
          {{paginationHeader()}}
        </b-col>
      </b-row>
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

               :items="filtered"
               :fields="fields">

        <template slot="top-row" slot-scope="{ fields }">
          <td v-for="field in fields" :key="field.key">
            <input
                    v-if="field.key !== 'actions'"
                    v-model="filters[field.key]"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >

          </td>
        </template>

        <template slot="create_date" slot-scope="row">
          {{row.item.create_date | dateFormat}}
        </template>

        <template slot="update_date" slot-scope="row">
          {{row.item.update_date | dateFormat}}
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

  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";
  import {ProjectHistoryManager} from "../../../../managers/ProjectHistoryManager";


  export default {
    components: {
      moment,
    },

    mixins: [],

    data: () => ({
      totalRows: 0,
      perPage: 50,
      currentPage: 1,
      sortBy: null,
      sortDesc: false,
      sortDirection: 'asc',
      filter: null,

      fields: [],

      filters: {
        status: '',

        user_name_create: '',
        create_date: '',
        user_name_update: '',
        update_date: '',
      },

      items: [],
    }),
    props: {
      projectId: {type: String, require: true},
    },
    created: function() {
      this.projectHistoryManager = new ProjectHistoryManager();
      this.getProjectHistory();

      this.setDefaultInterfaceData();
    },

    methods: {
      getProjectHistory: function () {
        this.projectHistoryManager.getAllByProjectId(this.projectId)
                .then( (response) => {
                  if(response.data !== false){
                    this.items = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      getFilterModelValue(key){
        return this.filters[key];
      },
      paginationHeader(){
        var from = (this.perPage * this.currentPage) - this.perPage + 1;
        var to = (this.perPage * this.currentPage);

        if(this.totalRows < this.perPage){
          return '1-'+ this.totalRows +' '+this.$store.state.t('of')+' ' + this.totalRows;
        } else {
          return from +'-'+ to +' '+this.$store.state.t('of')+' ' + this.totalRows;
        }
      },

      setDefaultInterfaceData: function () {
        this.customDialogfrontString = this.$store.state.t('Please stand by');
        this.fields = [
          { key: 'id', sortable: true},
          { key: 'status', label: this.$store.state.t('Status')},

          { key: 'user_name_create', label: this.$store.state.t('User'), sortable: true},
          { key: 'create_date', label: this.$store.state.t('Date'), sortable: true},
        ]
      }
    },

    beforeDestroy () {

    },

    filters: {
      dateFormat: function (date) {
        if (date === 'undefined' || date === null){
          return ''
        }

        return moment(String(date)).format('YYYY-MM-DD')
      },
    },
    computed: {
      filtered () {
        const filtered = this.items.filter(item => {
          return Object.keys(this.filters).every(key =>
                  String(item[key]).toLowerCase().includes(this.getFilterModelValue(key).toString().toLowerCase())
          )
        });

        this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    }
  }
</script>
