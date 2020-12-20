<template>
  <div>
    <page-title :button-action-hide="getACL().create !== true" :heading="$store.state.t('Consolidated Report')" :subheading="$store.state.t('Consolidated Report')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card v-if="!showFilter && getACL().list === true" :title="$store.state.t('Filters')" class="main-card mb-4">
      <a href="#" @click.prevent="toggleFilter">{{$store.state.t('Show Filter')}}</a>
    </b-card>
    <b-card v-else-if="showFilter && getACL().list === true" :title="$store.state.t('Filters')" class="main-card mb-4">
        <b-row class="mb-3">
          <b-col md="4" class="my-1" style="color: grey">
            <date-picker
                format="YYYY-MM-DD"
                :clearable="false" :firstDayOfWeek="1" :confirm="true" :placeholder="$store.state.t('Date')+': '+$store.state.t('From - To')" :shortcuts="[]"
                v-model="filters.date_range" lang="ru" range @change="getByFilter()">
            </date-picker>
          </b-col>
        </b-row>
        <b-row class="mb-3">
        <b-col md="4" class="my-1" style="color: grey">
          <v-select
              style="padding-top:0; margin-top:0; height:21px"
              v-on:change="getByFilter()"
              v-model="filters.ownCompanyIds"
              :items="ownCompanyItems"
              item-value="id"
              item-text="company"
              :placeholder="$store.state.t('Own Company')"
              multiple
              solo
              @blur="getByFilter"
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="true">{{$store.state.t('item(s)')}}</span>
            </template>
          </v-select>
        </b-col>
        <b-col md="4" class="my-1" style="color: grey">
          <v-select
              style="padding-top:0; margin-top:0; height:21px"
              v-on:change="getByFilter()"
              v-model="filters.financeClassIds"
              :items="financeClassItems"
              item-value="id"
              item-text="name"
              :placeholder="$store.state.t('Finance Classes')"
              multiple
              solo
              @blur="getByFilter"
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="true">{{$store.state.t('item(s)')}}</span>
            </template>
          </v-select>
        </b-col>
      </b-row>
        <b-row md="12">
          <b-col>
            <b-button @click="getByFilter" variant="primary" size="lg">{{$store.state.t('Search')}}</b-button>
            &nbsp;
            <a href="#" @click.prevent="toggleFilter">{{$store.state.t('Hide Filter')}}</a>
          </b-col>
        </b-row>
    </b-card>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Consolidated Report')" class="main-card mb-4">
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

          </td>
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
  </div>
</template>

<script>

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import loadercustom from "../../../components/loadercustom";
  import confirmator from "../../../components/confirmator";

  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";
  import accessMixin from "../../../../mixins/accessMixin";
  import {ProjectCompletingReportManager} from "../../../../managers/ProjectCompletingReportManager";
  import constantsMixin from "../../../../mixins/constantsMixin";
  import DatePicker from 'vue2-datepicker'
  import {FinanceClassesManager} from "@/managers/FinanceClassesManager";
  import {OwnCompaniesManager} from "@/managers/OwnCompaniesManager";
  import('../../../../css/ProjectCompleting.css')

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      DatePicker,
      moment,
    },

    mixins: [accessMixin, constantsMixin],

    data: () => ({
      accessLabelId: 'consolidatedReport',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      showFilter: true,

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
        date_range: [],
        financeClassIds: [],
        ownCompanyIds: [],
      },

      ownCompanyItems: [],
      financeClassItems: [],
      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);

      this.projectCompletingReportManager = new ProjectCompletingReportManager();
      this.financeClassesManager = new FinanceClassesManager();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.getProjects();
      this.getOwnCompanies();
      this.getFinanceClasses();


      this.setDefaultInterfaceData();
    },

    methods: {
      toggleFilter: function(){
        this.showFilter = !this.showFilter;
      },
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
          ['date_range'].forEach(function (key) {
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
      getFinanceClasses: function() {
        this.financeClassesManager.getAllForSelect()
            .then( (response) => {
              if(response.data !== false){
                this.financeClassItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
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

      getOwnCompanies: function () {
        this.ownCompaniesManager.getAll()
            .then( (response) => {
              if(response.data !== false){
                this.ownCompanyItems = response.data.items;
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
          { key: 'performer_own_company', label: this.$store.state.t('Performer'), sortable: true},
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
