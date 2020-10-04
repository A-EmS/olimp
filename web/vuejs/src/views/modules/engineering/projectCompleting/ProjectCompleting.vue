<template>
  <div>
    <page-title :button-action-hide="getACL().create !== true" :heading="$store.state.t('Project Completing')" :subheading="$store.state.t('Projects Completing Report')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Projects')" class="main-card mb-4">
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
    <v-alert
            v-if="!this.loadingProcess && getACL().list !== true"
            :value="true"
            color="error"
            icon="warning"
            outline
    >
      {{$store.state.t("You don't have permissions for it")}}
    </v-alert>
    <loadercustom :showDialog="this.loadingProcess" frontString="Permission checking..."></loadercustom>


    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import loadercustom from "../../../components/loadercustom";
  import confirmator from "../../../components/confirmator";

  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";
  import {CountriesManager} from "../../../../managers/CountriesManager";
  import {ProjectsManager} from "../../../../managers/ProjectsManager";
  import accessMixin from "../../../../mixins/accessMixin";
  import {ProjectCompletingReportManager} from "../../../../managers/ProjectCompletingReportManager";


  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      moment,
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'projectCompleting',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,


      totalRows: 0,
      perPage: 50,
      currentPage: 1,
      sortBy: null,
      sortDesc: false,
      sortDirection: 'asc',
      filter: null,

      fields: [],

      filters: {
        id: '',
        country: '',
        object_crypt: '',
        name: '',
        object_name: '',
        stamp: '',
        performer_own_company: '',
        customer_contractor: '',
        payer_contractor: '',
        // contract: '',
        payer_manager_individual: '',
        project_manager_individual: '',
        finance_document_date: '',
        // archive: '',
        notice: '',
      },

      countriesForFilter: [],
      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);
      this.countriesManager = new CountriesManager();
      this.projectsManager = new ProjectsManager();
      this.projectCompletingReportManager = new ProjectCompletingReportManager();
      this.getProjects();


      this.setDefaultInterfaceData();
    },

    methods: {
      getProjects: function () {
        this.projectCompletingReportManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.items = response.data.items;
                    this.totalRows = response.data.items.length;
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
          { key: 'object_crypt', label: this.$store.state.t('Object Crypt'), sortable: true},
          { key: 'payer_contractor', label: this.$store.state.t('Payer'), sortable: true},
          { key: 'customer_contractor', label: this.$store.state.t('Customer'), sortable: true},
          { key: 'payer_manager_individual', label: this.$store.state.t('Payer Manager'), sortable: true},
          { key: 'project_manager_individual', label: this.$store.state.t('Project Manager'), sortable: true},
          { key: 'performer_own_company', label: this.$store.state.t('Performer'), sortable: true},

          // { key: 'other_services', label: this.$store.state.t('Other services'), sortable: true},
          { key: 'finance_document_date', label: this.$store.state.t('Finance Document Date'), sortable: true},


          { key: 'notice', label: this.$store.state.t('Notice'), sortable: true},

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
