<template>
  <div>
    <page-title :button-action-hide="getACL().create !== true" :heading="$store.state.t('Project Completing')" :subheading="$store.state.t('Projects Completing Report')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Projects')" class="main-card mb-4">
      <b-row class="mb-3">
        <b-col md="6" class="my-1">
          <b-pagination v-on:change="getByPage()" :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
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

               :sort-by.sync="sortBy"
               :sort-desc.sync="sortDesc"
               :sort-direction="sortDirection"

               :items="items"
               :fields="fields">


        <template slot="top-row" slot-scope="{ fields }">
          <td v-for="field in fields" :key="field.key">
            <input
                    v-if="['id', 'object_crypt', 'payer_contractor', 'customer_contractor', 'notice'].indexOf(field.key) !== -1"
                    v-on:change="getByFilter()"
                    v-model="filters[field.key]"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >

            <date-picker
                    v-if="['start_date', 'end_date', 'act_date', 'finance_document_date'].indexOf(field.key) !== -1"
                    format="YYYY-MM-DD"
                    style="width:100px" :clearable="false" :firstDayOfWeek="1" :confirm="true" :placeholder="$store.state.t('From - To')" :shortcuts="[]"
                    v-model="filters[field.key]" lang="ru" range @change="getByFilter()"></date-picker>

            <select
                    v-if="field.key=='other_services'"
                    v-model="filters['other_services']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
                    v-on:change="getByFilter()"
            >
              <option value="">{{$store.state.t('----')}}</option>
              <option value="0">{{$store.state.t('No')}}</option>
              <option value="1">{{$store.state.t('Yes')}}</option>
            </select>


            <v-select
                    v-if="field.key=='performer_own_company'"
                    style="padding-top:0; margin-top:0; height:21px"
                    v-model="filters[field.key]"
                    :items="performerOwnCompanyItems"
                    item-value="id"
                    item-text="name"
                    multiple
                    solo
                    @blur="getByFilter"
            >
              <template v-slot:selection="{ item, index }">
                  <span v-if="index === 0">{{ filters[field.key].length }} {{$store.state.t('item(s)')}}</span>
              </template>
            </v-select>

            <v-select
                    v-if="field.key=='payer_manager_individual'"
                    style="padding-top:0; margin-top:0; height:21px"
                    v-model="filters[field.key]"
                    :items="payerManagerIndividualItems"
                    item-value="id"
                    item-text="name"
                    multiple
                    solo
                    @blur="getByFilter"
            >
              <template v-slot:selection="{ item, index }">
                <span v-if="index === 0">{{ filters[field.key].length }} {{$store.state.t('item(s)')}}</span>
              </template>
            </v-select>

            <v-select
                    v-if="field.key=='project_manager_individual'"
                    style="padding-top:0; margin-top:0; height:21px"
                    v-model="filters[field.key]"
                    :items="projectManagerIndividualItems"
                    item-value="id"
                    item-text="name"
                    multiple
                    solo
                    @blur="getByFilter"
            >
              <template v-slot:selection="{ item, index }">
                <span v-if="index === 0">{{ filters[field.key].length }} {{$store.state.t('item(s)')}}</span>
              </template>
            </v-select>
          </td>
        </template>

        <template slot="other_services" slot-scope="row">
          <a href="#" v-if="row.item.other_services=='Yes'" @click="showOtherServices(row.item.finance_document_id)">{{$store.state.t(row.item.other_services)}}</a>
          <span v-else>{{$store.state.t(row.item.other_services)}}</span>
        </template>

        <template slot="start_date" slot-scope="row">
          {{row.item.start_date | dateFormat}}
        </template>

        <template slot="end_date" slot-scope="row">
          {{row.item.end_date | dateFormat}}
        </template>

      </b-table>

      <b-row>
        <b-col md="6" class="my-1">
          <b-pagination v-on:change="getByPage()" :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
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
    <additional-service-document-info></additional-service-document-info>
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
  import accessMixin from "../../../../mixins/accessMixin";
  import {ProjectCompletingReportManager} from "../../../../managers/ProjectCompletingReportManager";
  import AdditionalServiceDocumentInfo from "../../../components/additionalServiceDocumentInfo";
  import constantsMixin from "../../../../mixins/constantsMixin";
  import DatePicker from 'vue2-datepicker'
  import('../../../../css/ProjectCompleting.css')

  export default {
    components: {
      AdditionalServiceDocumentInfo,
      PageTitle,
      loadercustom,
      confirmator,
      DatePicker,
      moment,
    },

    mixins: [accessMixin, constantsMixin],

    data: () => ({
      accessLabelId: 'projectCompleting',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,

      totalRows: 0,
      perPage: 100,
      currentPage: 1,
      sortBy: null,
      sortDesc: false,
      sortDirection: 'asc',
      filter: null,

      fields: [],

      filters: {
        id: '',
        object_crypt: '',
        performer_own_company: [],
        customer_contractor: '',
        payer_contractor: '',
        other_services: '',
        start_date: [],
        end_date: [],
        act_date: [],

        payer_manager_individual: [],
        project_manager_individual: [],
        finance_document_date: [],

        notice: '',
      },

      payerManagerIndividualItems: [],
      projectManagerIndividualItems: [],
      performerOwnCompanyItems: [],

      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);
      this.countriesManager = new CountriesManager();
      this.projectCompletingReportManager = new ProjectCompletingReportManager();
      this.getProjects();
      this.getPayerManagers();
      this.getProjectMangers();
      this.getPerformers();


      this.setDefaultInterfaceData();
    },

    methods: {
      getByPage: function () {
        this.$nextTick(() => {
          this.showCustomLoaderDialog = true;
          this.projectCompletingReportManager.getByPage(this.currentPage, this.perPage, this.filters)
                  .then( (response) => {
                    if(response.data !== false){
                      this.items = response.data.items;
                      this.showCustomLoaderDialog = false;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      },

      getByFilter: function () {
        this.$nextTick(() => {
          var self = this;
          ['finance_document_date','start_date','end_date','act_date',].forEach(function (key) {
            if (self.filters[key].length > 0 && self.filters[key][0] !== null) {
              self.filters[key][0] = moment(self.filters[key][0]).format("YYYY-MM-DD");
              self.filters[key][1] = moment(self.filters[key][1]).format("YYYY-MM-DD");
            }
          });

          this.showCustomLoaderDialog = true;
          this.projectCompletingReportManager.getByPage(1, this.perPage, this.filters)
                  .then((response) => {
                    if (response.data !== false) {
                      this.items = response.data.items;
                      this.totalRows = response.data.count;
                      this.currentPage = 1;
                      this.showCustomLoaderDialog = false;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      },
      getProjects: function () {
        var emptyFilter = {};
        this.projectCompletingReportManager.getByPage(this.currentPage, this.perPage, emptyFilter)
                .then( (response) => {
                  if(response.data !== false){
                    this.items = response.data.items;
                    this.totalRows = response.data.count;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      getPayerManagers: function () {
        this.projectCompletingReportManager.getPayerManagers()
                .then( (response) => {
                  if(response.data !== false){
                    this.payerManagerIndividualItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getProjectMangers: function () {
        this.projectCompletingReportManager.getProjectMangers()
                .then( (response) => {
                  if(response.data !== false){
                    this.projectManagerIndividualItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getPerformers: function () {
        this.projectCompletingReportManager.getPerformers()
                .then( (response) => {
                  if(response.data !== false){
                    this.performerOwnCompanyItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      showOtherServices: function(financeDocumentId) {
        this.$eventHub.$emit(this.constants.showAdditionalServiceDocumentInfoModal, {financeDocumentId: financeDocumentId});
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

          { key: 'other_services', label: this.$store.state.t('Other services'), sortable: true},
          { key: 'finance_document_date', label: this.$store.state.t('Finance Document Date'), sortable: true},
          { key: 'start_date', label: this.$store.state.t('Start Date'), sortable: true},
          { key: 'end_date', label: this.$store.state.t('End Date'), sortable: true},
          { key: 'act_date', label: this.$store.state.t('Act Date'), sortable: true},


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
