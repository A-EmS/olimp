<template>
  <div>
    <page-title :button-action-hide="true" :heading="$store.state.t('Consolidated Report')" :subheading="$store.state.t('Consolidated Report')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card v-if="!showFilter && getACL().list === true" :title="$store.state.t('Filters')" class="main-card mb-4">
      <a href="#" @click.prevent="toggleFilter">{{$store.state.t('Show Filter')}}</a>
    </b-card>
    <b-card v-else-if="showFilter && getACL().list === true" :title="$store.state.t('Filters')" class="main-card mb-4">
        <b-row class="mb-3">
          <b-col md="4" class="my-1" style="color: grey">
            <date-picker
                format="YYYY-MM-DD"
                :clearable="false" :firstDayOfWeek="1" :confirm="true" :placeholder="$store.state.t('Date')+': '+$store.state.t('From - To')" :shortcuts="[]"
                v-model="filters.report_period" lang="ru" range>
            </date-picker>
          </b-col>
        </b-row>
        <b-row class="mb-3">
        <b-col md="4" class="my-1" style="color: grey">
          <v-select
              style="padding-top:0; margin-top:0; height:21px"
              v-model="filters.ownCompanyIds"
              :items="ownCompanyItems"
              item-value="id"
              item-text="company"
              :placeholder="$store.state.t('Own Company')"
              multiple
              solo
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="filters.ownCompanyIds.length <= 1">{{item.company}}</span>
              <span v-if="index === 0 && filters.ownCompanyIds.length > 1">{{filters.ownCompanyIds.length + ' ' + $store.state.t('item(s)')}}</span>
            </template>
          </v-select>
        </b-col>
        <b-col md="4" class="my-1" style="color: grey">
          <v-select
              style="padding-top:0; margin-top:0; height:21px"
              v-model="filters.financeClassIds"
              :items="financeClassItems"
              item-value="id"
              item-text="name"
              :placeholder="$store.state.t('Finance Classes')"
              multiple
              solo
          >
            <template v-slot:selection="{ item, index }">
              <span v-if="filters.financeClassIds.length <= 1">{{item.name}}</span>
              <span v-if="index === 0 && filters.financeClassIds.length > 1">{{filters.financeClassIds.length + ' ' + $store.state.t('item(s)')}}</span>
            </template>
          </v-select>
        </b-col>

          <b-col md="4" class="my-1" style="color: grey">
            <v-select
                style="padding-top:0; margin-top:0; height:21px"
                v-model="filters.paymentTypeIds"
                :items="paymentTypeItems"
                item-value="id"
                item-text="name"
                :placeholder="$store.state.t('Payment Type')"
                multiple
                solo
            >
              <template v-slot:selection="{ item, index }">
                <span v-if="filters.paymentTypeIds.length <= 1">{{item.name}}</span>
                <span v-if="index === 0 && filters.paymentTypeIds.length > 1">{{filters.paymentTypeIds.length + ' ' + $store.state.t('item(s)')}}</span>
              </template>
            </v-select>
          </b-col>
      </b-row>
        <b-row md="12">
          <b-col>
            <b-button @click="getByFilter" variant="primary" size="lg">{{$store.state.t('Create Report')}}</b-button>
            &nbsp;
            <b-button @click="saveToFile" variant="success" size="lg">{{$store.state.t('Save To File')}}</b-button>
            &nbsp;
            <a href="#" @click.prevent="toggleFilter">{{$store.state.t('Hide Filter')}}</a>
          </b-col>
        </b-row>
    </b-card>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Consolidated Report')" class="main-card mb-4">
      <b-table :striped="true"
               :bordered="true"
               :outlined="true"
               :small="true"
               :hover="false"
               :dark="false"
               :fixed="false"
               :foot-clone="true"
               :tbody-tr-class="rowClass"

               :sort-by.sync="sortBy"
               :sort-desc.sync="sortDesc"
               :sort-direction="sortDirection"

               :items="items"
               :fields="fields">

        <template slot="№" slot-scope="row">
            <div style="float: right">{{row.item['№']}}</div>
        </template>

        <template slot="financeClass" slot-scope="row">
          <div :style="financeClassStyle(row.item)">{{row.item['financeClass']}}</div>
        </template>

      </b-table>
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
  import constantsMixin from "../../../../mixins/constantsMixin";
  import mathMixin from "../../../../mixins/mathMixin";
  import DatePicker from 'vue2-datepicker'
  import {FinanceClassesManager} from "@/managers/FinanceClassesManager";
  import {OwnCompaniesManager} from "@/managers/OwnCompaniesManager";
  import {ConsolidatedReportManager} from "@/managers/ConsolidatedReportManager";
  import {PaymentTypeManager} from "@/managers/PaymentTypeManager";
  import('../../../../css/ProjectCompleting.css')

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      DatePicker,
      moment,
    },

    mixins: [accessMixin, constantsMixin, mathMixin],

    data: () => ({
      accessLabelId: 'consolidatedReport',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      showFilter: true,

      sortBy: null,
      sortDesc: false,
      sortDirection: 'asc',
      filter: null,

      fields: [],

      filters: {
        report_period: [],
        financeClassIds: [],
        ownCompanyIds: [],
        paymentTypeIds: [],
        saveReportToFile: 0,
      },

      ownCompanyItems: [],
      financeClassItems: [],
      paymentTypeItems: [],
      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);

      this.consolidatedReportManager = new ConsolidatedReportManager();
      this.paymentTypeManager = new PaymentTypeManager();
      this.financeClassesManager = new FinanceClassesManager();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.getDataForReport();
      this.getOwnCompanies();
      this.getFinanceClasses();
      this.getPaymentTypes();


      this.setDefaultInterfaceData();
    },

    methods: {
      toggleFilter: function(){
        this.showFilter = !this.showFilter;
      },

      saveToFile: function () {
        this.$nextTick(() => {
          this.filters.saveReportToFile = 1;

          var self = this;
          ['report_period'].forEach(function (key) {
            if (self.filters[key].length > 0 && self.filters[key][0] !== null) {
              self.filters[key][0] = moment(self.filters[key][0]).format("YYYY-MM-DD");
              self.filters[key][1] = moment(self.filters[key][1]).format("YYYY-MM-DD");
            }
          });

          this.showCustomLoaderDialog = true;
          this.consolidatedReportManager.getByFilter(this.filters)
              .then((response) => {
                if (response.data !== false) {
                  this.saveReportToFile = 0;
                  this.showCustomLoaderDialog = false;
                  this.items = response.data.items;
                  this.fields = response.data.fields;
                  if (response.data.reportCsvFilePath !== false) {
                    console.log(response.data.reportCsvFilePath);
                    window.open(window.apiDomainUrl+'/'+response.data.reportCsvFilePath);
                  }
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
          ['report_period'].forEach(function (key) {
            if (self.filters[key].length > 0 && self.filters[key][0] !== null) {
              self.filters[key][0] = moment(self.filters[key][0]).format("YYYY-MM-DD");
              self.filters[key][1] = moment(self.filters[key][1]).format("YYYY-MM-DD");
            }
          });

          this.showCustomLoaderDialog = true;
          this.consolidatedReportManager.getByFilter(this.filters)
                  .then((response) => {
                    if (response.data !== false) {
                      this.items = response.data.items;
                      this.fields = response.data.fields;
                      this.showCustomLoaderDialog = false;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      },
      getPaymentTypes: function() {
        this.paymentTypeManager.getAll()
            .then( (response) => {
              if(response.data !== false){
                this.paymentTypeItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
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
      getDataForReport: function () {
        var emptyFilter = {};
        this.consolidatedReportManager.getByFilter(emptyFilter)
                .then( (response) => {
                  if(response.data !== false){
                    this.items = response.data.items;
                    this.fields = response.data.fields;
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

      financeClassStyle: function (item){
        var marginByDepth = 0;

        if (item.depth !== 1) {
          marginByDepth = (item.depth - 1) * 10;
        }

        return 'margin-left: '+marginByDepth+'px';
      },

      rowClass: function (item){
        let classString = '';
        if (item.depth === 1) {
          if (item.paymentOperationTypeId === 1) {
            classString += ' mainTotal ';
          } else if (item.paymentOperationTypeId === 2) {
            classString += ' mainTotalExpend ';
          }

          if (typeof item.specialClass !== 'undefined'){
            classString += ' ' + item.specialClass + ' ';
          }
        }

        if (typeof item.childList !== 'undefined' && item.childList.length > 0) {
          classString += ' notLeaf ';
        }

        return classString;
      },

      getFilterModelValue(key){
        return this.filters[key];
      },

      setDefaultInterfaceData: function () {
        this.customDialogfrontString = this.$store.state.t('Please stand by');
        this.fields = [
          // { key: '№', sortable: true},
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

<style>
.mainTotal {
  background-color: #c8eedb !important;
  font-weight: bold;
}

.mainTotalExpend {
  background-color: #F2C6C6 !important;
  font-weight: bold;
}

.greatIncome {
  border-top: solid 3px;
  background-color: #c8eedb !important;
  font-weight: bold;
}

.greatExpense {
  background-color: #F2C6C6 !important;
  font-weight: bold;
}

.greatTotals {
  background-color: #EEEEEE !important;
  font-weight: bold;
}

.notLeaf {
  font-weight: bold;
}
</style>