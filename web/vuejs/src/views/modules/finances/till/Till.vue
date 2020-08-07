<template>
  <div>
    <page-title :button-action-hide="getACL().create !== true" :createProcessName="createProcessName" :heading="$store.state.t('Till')" :subheading="$store.state.t('Till actions')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <form_component v-if="getACL().update === true" :till_id="filters.till_id" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_component>

    <div class="row" v-if="getACL().list === true && filters.till_id > 0">
      <div class="col-md-6 col-lg-3">
        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-warning border-warning card">
          <div class="widget-chat-wrapper-outer">
            <div class="widget-chart-content">
              <div class="widget-title text-uppercase">{{$store.state.t('Balance')}}: <span class="text-dark">₴ 232.00</span></div>
              <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                <div class="widget-chart-flex align-items-center">
                  <div class="text-success">
                    <small class="opacity-5 pr-1">₴</small>
                    3175.00
                  </div>
                  <div class="ml-3 text-danger">
                    <small class="opacity-5 pr-1">₴</small>
                    1212.00
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-warning border-warning card">
          <div class="widget-chat-wrapper-outer">
            <div class="widget-chart-content">
              <div class="widget-title text-uppercase">{{$store.state.t('Balance')}}: <span class="text-dark">€ 232.00</span></div>
              <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                <div class="widget-chart-flex align-items-center">
                  <div class="text-success">
                    <small class="opacity-5 pr-1">€</small>
                    3175.00
                  </div>
                  <div class="ml-3 text-danger">
                    <small class="opacity-5 pr-1">€</small>
                    1212.00
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row" v-if="getACL().list === true && filters.till_id > 0">
      <div class="col-md-12 col-lg-6 col-xl-6">
        <b-card class="mb-3 nav-justified" no-body>
          <b-tabs class="card-header-tab-animation" card>
            <b-tab :title="$store.state.t('Balance Moving')" active v-on:click="toggleShowingBalanceMoving()">
              <div v-if="showBalanceMoving">
                <div>
                  <v-select
                          :items="[{id: 1, name: 'Till 1'}, {id: 2, name: 'Till 2'}]"
                          item-value="id"
                          item-text="name"
                          :label="$store.state.t('Target Till')"
                  ></v-select>
                  <v-select
                          :items="[{id: 1, currency_name: 'USD'}, {id: 2, currency_name: 'UAH'}]"
                          item-value="id"
                          item-text="currency_name"
                          :label="$store.state.t('Currency To Move')"
                  ></v-select>
                  <v-text-field
                          :label="$store.state.t('I Will Move')"
                          required
                          type="number"
                          min="0"
                          step="0.01"
                  ></v-text-field>
                  <v-btn color="success" @click="">{{$store.state.t('Submit')}}</v-btn>
                </div>
                <hr />
                <h6 class="text-muted text-uppercase font-size-md opacity-5 font-weight-normal">{{$store.state.t('Balance Moving History')}}</h6>
                <div class="scroll-area-sm">
                  <VuePerfectScrollbar class="scrollbar-container" v-once>
                    <ul class="rm-list-borders rm-list-borders-scroll list-group list-group-flush">
                      <li v-for="n in 3" class="list-group-item">
                        <div class="widget-content p-0">
                          <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                              <div class="widget-heading">Till 1 -> Till {{n}}</div>
                              <div class="widget-subheading">{{$store.state.t('Moved')}}</div>
                            </div>
                            <div class="widget-content-right">
                              <div class="font-size-xlg text-muted">
                                <small class="pr-1 text-danger">$</small>
                                <span class="text-danger">{{n}}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </li>
                      <li v-for="n in 3" class="list-group-item">
                        <div class="widget-content p-0">
                          <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                              <div class="widget-heading">Till {{n}} -> Till 1</div>
                              <div class="widget-subheading">{{$store.state.t('Got')}}</div>
                            </div>
                            <div class="widget-content-right">
                              <div class="font-size-xlg text-muted">
                                <small class="pr-1 text-success">₴</small>
                                <span class="text-success">{{n*30}}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </VuePerfectScrollbar>
                </div>
              </div>
            </b-tab>
          </b-tabs>
        </b-card>
      </div>
      <div class="col-md-12 col-lg-6 col-xl-6">
        <b-card class="mb-3 nav-justified" no-body>
          <b-tabs class="card-header-tab-animation" card>
            <b-tab :title="$store.state.t('Currency Exchange')" active v-on:click="toggleShowingCurrencyExchange()">
              <div v-if="showCurrencyExchange">
                <div>
                  <v-select
                          :items="[{id: 1, currency_name: 'USD'}, {id: 2, currency_name: 'UAH'}]"
                          item-value="id"
                          item-text="currency_name"
                          :label="$store.state.t('Currency To Sell')"
                  ></v-select>
                  <v-select
                          :items="[{id: 1, currency_name: 'USD'}, {id: 2, currency_name: 'UAH'}]"
                          item-value="id"
                          item-text="currency_name"
                          :label="$store.state.t('Currency To Buy')"
                  ></v-select>
                  <v-text-field
                          :label="$store.state.t('I will sell')"
                          required
                          type="number"
                          min="0"
                          step="0.01"
                  ></v-text-field>
                  <v-text-field
                          :label="$store.state.t('I will get')"
                          value=""
                          readonly=""
                  ></v-text-field>
                  <v-btn color="success" @click="">{{$store.state.t('Submit')}}</v-btn>
                </div>
                <hr />
                <h6 class="text-muted text-uppercase font-size-md opacity-5 font-weight-normal">{{$store.state.t('Currency Exchange History')}}</h6>
                <div class="scroll-area-sm">
                  <VuePerfectScrollbar class="scrollbar-container" v-once>
                    <ul class="rm-list-borders rm-list-borders-scroll list-group list-group-flush">
                      <li v-for="n in 10" class="list-group-item">
                        <div class="widget-content p-0">
                          <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                              <div class="widget-heading">USD -> UAH</div>
                              <div class="widget-subheading">{{$store.state.t('Exchanged')}}</div>
                            </div>
                            <div class="widget-content-right">
                              <div class="font-size-xlg text-muted">
                                <small class="pr-1 text-danger">$</small>
                                <span class="text-danger">{{n}}</span>
                                ->
                                <small class="pr-1 text-success">₴</small>
                                <span class="text-success">{{n*30}}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </VuePerfectScrollbar>
                </div>
              </div>
            </b-tab>
          </b-tabs>
        </b-card>
      </div>
    </div>
    <b-card v-if="getACL().list === true && filters.till_id > 0" :title="$store.state.t('Till')" class="main-card mb-4">
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
                    v-if="field.key !== 'actions' && field.key !== 'payment_operation_type' && field.key !== 'payment_type'
                    && field.key !== 'finance_class' && field.key !== 'currency' && field.key !== 'document_status'
                    && field.key !== 'finance_action'"
                    v-model="filters[field.key]"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
                    v-on:change="getByFilter()"
            >

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='payment_operation_type'"
                    v-model="filters['payment_operation_type_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Payment Operation Types')}}</option>
              <option v-for="item in paymentOperationTypeItems" :value="item.id">{{item.name}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='payment_type'"
                    v-model="filters['payment_type_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Payment Types')}}</option>
              <option v-for="item in paymentTypeItems" :value="item.id">{{item.name}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='finance_class'"
                    v-model="filters['finance_class_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Finance Classes')}}</option>
              <option v-for="item in financeClassItems" :value="item.id">{{item.name}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='currency'"
                    v-model="filters['currency_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Currencies')}}</option>
              <option v-for="item in currencyItems" :value="item.id">{{item.currency_name}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='document_status'"
                    v-model="filters['document_status_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Document Statuses')}}</option>
              <option v-for="item in documentStatusItems" :value="item.id">{{item.name}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='finance_action'"
                    v-model="filters['finance_action_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Finance Actions')}}</option>
              <option v-for="item in financeActionItems" :value="item.id">{{item.name}}</option>
            </select>

          </td>
        </template>

        <template slot="create_date" slot-scope="row">
          {{row.item.create_date | dateFormat}}
        </template>

        <template slot="update_date" slot-scope="row">
          {{row.item.update_date | dateFormat}}
        </template>

        <template slot="actions" slot-scope="row">
          <table>
            <tr>
              <td v-if="getACL().update === true"><i class='lnr-pencil' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRow(parseInt(row.item.id))"> </i></td>
              <td v-if="getACL().delete === true"><i class='lnr-trash' size="sm" style="cursor: pointer; font-size: large; color: red" @click.stop="" @click="confirmDeleteRow(parseInt(row.item.id), row.item.id)"> </i></td>
            </tr>
          </table>
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
    <v-alert
            v-if="!this.loadingProcess && !this.loadTillProcess && getACL().list === true && filters.till_id == null"
            :value="true"
            color="error"
            icon="warning"
            outline
    >
      {{$store.state.t("There is no registered till in system for you")}}
    </v-alert>
    <loadercustom :showDialog="this.loadingProcess" frontString="Permission checking..."></loadercustom>


    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
    <confirmator
            :handlerInputProcessName="confirmatorInputProcessName"
            :handlerOutputProcessName="confirmatorOutputProcessName">
    </confirmator>
  </div>
</template>

<script>

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import loadercustom from "../../../components/loadercustom";
  import confirmator from "../../../components/confirmator";
  import form_component from "./form_component";
  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";
  import accessMixin from "../../../../mixins/accessMixin";
  import {OrdersManager} from "../../../../managers/OrdersManager";
  import {PaymentOperationTypeManager} from "../../../../managers/PaymentOperationTypeManager";
  import {FinanceClassesManager} from "../../../../managers/FinanceClassesManager";
  import {CurrenciesManager} from "../../../../managers/CurrenciesManager";
  // import {OwnCompaniesManager} from "../../../../managers/OwnCompaniesManager";
  import {PaymentTypeManager} from "../../../../managers/PaymentTypeManager";
  import {DocumentStatusesManager} from "../../../../managers/DocumentsStatusesManager";
  import {FinanceActionsManager} from "../../../../managers/FinanceActionsManager";
  import VuePerfectScrollbar from "vue-perfect-scrollbar";
  import Options from "../../../../DemoPages/Vuetify/Components/scrolling/options";
  import {TillsManager} from "../../../../managers/TillsManager";

  export default {
    components: {
      Options,
      PageTitle,
      loadercustom,
      confirmator,
      form_component,
      moment,
      VuePerfectScrollbar
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'till',
      loadTillProcess: false,
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,
      showCurrencyExchange: false,
      showBalanceMoving: false,

      updateItemListEventName: 'updateList:tillOperation',
      createProcessName: 'create:tillOperation',
      updateProcessName: 'update:tillOperation',
      confirmatorInputProcessName: 'confirm:deleteTillOperation',
      confirmatorOutputProcessName: 'confirmed:deleteTillOperation',

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
        payment_operation_type_id: '',
        payment_type_id: '',
        finance_class_id: '',
        contractor: '',
        date: '',
        report_period: '',
        currency_id: '',
        amount: '',
        document_status_id: '',
        notice: '',
        till_id: null,
        // base_document: '',
        // base_document_content: '',
        // own_company_id: '',
        // payment_account: '',
        finance_action_id: '',

        user_name_create: '',
        create_date: '',
        user_name_update: '',
        update_date: '',
      },

      paymentOperationTypeItems: [],
      paymentTypeItems: [],
      financeClassItems: [],
      currencyItems: [],
      documentStatusItems: [],
      // ownCompanyItems: [],
      financeActionItems: [],
      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);

      this.tillsManager = new TillsManager();

      this.paymentOperationTypeManager = new PaymentOperationTypeManager();
      this.paymentTypeManager = new PaymentTypeManager();
      this.financeClassesManager = new FinanceClassesManager();
      this.currenciesManager = new CurrenciesManager();
      this.documentStatusManager = new DocumentStatusesManager();
      // this.ownCompaniesManager = new OwnCompaniesManager();
      this.financeActionsManager = new FinanceActionsManager();
      this.ordersManager = new OrdersManager();

      this.loadTill();

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.deleteRow(data.id);
      });

      this.$eventHub.$on(this.updateItemListEventName, (data) => {
        this.getTillOperations();
      });

      this.setDefaultInterfaceData();
    },

    methods: {
      loadTill: function (){
        this.loadTillProcess = true;
        this.tillsManager.loadTillForUser()
          .then( (response) => {
            this.loadTillProcess = false;
            if(response.data !== false && response.data !== 0){
              this.filters.till_id = response.data;

              this.getPaymentOperationTypes();
              this.getPaymentTypes();
              this.getFinanceClasses();
              this.getCurrencies();
              this.getDocumentsStatuses();
              // this.getOwnCompanies();
              this.getFinanceActions();
              this.getTillOperations();

            }
          })
          .catch(function (error) {
            console.log(error);
          });
      },
      getTillOperations: function () {
        this.ordersManager.getTillOperationsByPage(this.currentPage, this.perPage, this.filters)
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

      getByPage: function () {
        this.$nextTick(() => {
          this.showCustomLoaderDialog = true;
          this.ordersManager.getTillOperationsByPage(this.currentPage, this.perPage, this.filters)
                  .then( (response) => {
                    if(response.data !== false){
                      this.items = response.data.items;
                      this.showCustomLoaderDialog = false;
                      // this.totalRows = response.data.items.length;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      },

      getByFilter: function () {
        this.$nextTick(() => {
          this.showCustomLoaderDialog = true;
          this.ordersManager.getTillOperationsByPage(1, this.perPage, this.filters)
                  .then( (response) => {
                    if(response.data !== false){
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

      getPaymentOperationTypes: function() {
        this.paymentOperationTypeManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.paymentOperationTypeItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
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
      getCurrencies: function() {
        this.currenciesManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.currencyItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getDocumentsStatuses: function() {
        this.documentStatusManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.documentStatusItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      // getOwnCompanies: function () {
      //   this.ownCompaniesManager.getAll()
      //           .then( (response) => {
      //             if(response.data !== false){
      //               this.ownCompanyItems = response.data.items;
      //             }
      //           })
      //           .catch(function (error) {
      //             console.log(error);
      //           });
      // },
      getFinanceActions: function() {
        this.financeActionsManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.financeActionItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      toggleShowingCurrencyExchange: function(){
        this.showCurrencyExchange = !this.showCurrencyExchange;
      },

      toggleShowingBalanceMoving: function(){
        this.showBalanceMoving = !this.showBalanceMoving;
      },

      updateRow: function(id){
        window.scrollToTop();
        this.$eventHub.$emit(this.updateProcessName, {id: id});
      },

      confirmDeleteRow: function(id, name){
        this.$eventHub.$emit(this.confirmatorInputProcessName, {
          titleString: this.$store.state.t('Deleting') + '...',
          confirmString: this.$store.state.t('Confirm delete') +  ' ' + this.$store.state.t('Invoices') +'..'+name,
          idToConfirm: id
        });
      },

      deleteRow: function(id){
        this.showCustomLoaderDialog = true;
        this.customDialogfrontString= this.$store.state.t('Deleting') + '...'+id;
        this.ordersManager.delete({id:id})
                .then( (response) => {
                  if(response.data !== false){
                    if(response.data.status === true){

                      var currentIndex = this.items.indexOf(this.items.find(obj => obj.id == id));

                      delete(this.items[currentIndex]);
                      this.items = this.items.filter(function (el) {
                        return el != '';
                      });

                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, window.config.time_popup);
                    } else {
                      this.customDialogfrontString='Error...!!!!!!!!';
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, 3000);
                    }
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
          { key: 'actions', label: this.$store.state.t('Actions')},
          { key: 'finance_action', label: this.$store.state.t('Finance Action'), sortable: true},
          { key: 'payment_operation_type', label: this.$store.state.t('Payment Operation Type'), sortable: true},
          { key: 'payment_type', label: this.$store.state.t('Payment Type'), sortable: true},
          { key: 'finance_class', label: this.$store.state.t('Finance Class'), sortable: true},
          { key: 'contractor', label: this.$store.state.t('Contractor'), sortable: true},
          { key: 'date', label: this.$store.state.t('Date'), sortable: true},
          { key: 'report_period', label: this.$store.state.t('Report Period'), sortable: true},
          { key: 'currency', label: this.$store.state.t('Currency'), sortable: true},
          { key: 'amount', label: this.$store.state.t('Amount'), sortable: true},
          { key: 'document_status', label: this.$store.state.t('Document Status'), sortable: true},
          { key: 'notice', label: this.$store.state.t('Notice'), sortable: true},
          // { key: 'base_document', label: this.$store.state.t('Base Document'), sortable: true},
          // { key: 'base_document_content', label: this.$store.state.t('Base Document Content'), sortable: true},
          // { key: 'own_company', label: this.$store.state.t('Own Company'), sortable: true},
          // { key: 'payment_account', label: this.$store.state.t('Payment Account'), sortable: true},

          { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
          { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
          { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
          { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
        ]
      }
    },

    beforeDestroy () {
      this.$eventHub.$off(this.confirmatorOutputProcessName);
      this.$eventHub.$off(this.updateItemListEventName);
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

        // this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    }
  }
</script>
