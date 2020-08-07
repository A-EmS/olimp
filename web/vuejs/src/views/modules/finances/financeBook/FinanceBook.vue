<template>
  <div>
    <page-title :button-action-hide="false" :heading="$store.state.t('Finance Book')" :subheading="$store.state.t('Finance Book actions')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Finance Book')" class="main-card mb-4">
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
                    && field.key !== 'own_company' && field.key !== 'finance_action'"
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
                    v-if="field.key=='own_company'"
                    v-model="filters['own_company_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Own Companies')}}</option>
              <option v-for="item in ownCompanyItems" :value="item.id">{{item.company}}</option>
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
    <loadercustom :showDialog="this.loadingProcess" frontString="Permission checking..."></loadercustom>
    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import loadercustom from "../../../components/loadercustom";
  import confirmator from "../../../components/confirmator";
  var moment = require('moment');

  import accessMixin from "../../../../mixins/accessMixin";
  import {FinanceBookManager} from "../../../../managers/FinanceBookManager";
  import {PaymentOperationTypeManager} from "../../../../managers/PaymentOperationTypeManager";
  import {PaymentTypeManager} from "../../../../managers/PaymentTypeManager";
  import {FinanceClassesManager} from "../../../../managers/FinanceClassesManager";
  import {CurrenciesManager} from "../../../../managers/CurrenciesManager";
  import {DocumentStatusesManager} from "../../../../managers/DocumentsStatusesManager";
  import {OwnCompaniesManager} from "../../../../managers/OwnCompaniesManager";
  import {FinanceActionsManager} from "../../../../managers/FinanceActionsManager";

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      moment,
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'financeBook',
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
        till: '',
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
        base_document: '',
        base_document_content: '',
        own_company_id: '',
        payment_account: '',
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
      ownCompanyItems: [],
      financeActionItems: [],
      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);

      this.paymentOperationTypeManager = new PaymentOperationTypeManager();
      this.paymentTypeManager = new PaymentTypeManager();
      this.financeClassesManager = new FinanceClassesManager();
      this.currenciesManager = new CurrenciesManager();
      this.documentStatusManager = new DocumentStatusesManager();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.financeActionsManager = new FinanceActionsManager();

      this.getPaymentOperationTypes();
      this.getPaymentTypes();
      this.getFinanceClasses();
      this.getCurrencies();
      this.getDocumentsStatuses();
      this.getOwnCompanies();
      this.getFinanceActions();

      this.financeBookManager = new FinanceBookManager();
      this.getFinanceBookInfo();


      this.setDefaultInterfaceData();
    },

    methods: {
      getFinanceBookInfo: function () {
        this.financeBookManager.getInfoByPageAndFilters(this.currentPage, this.perPage, [])
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
          this.financeBookManager.getInfoByPageAndFilters(this.currentPage, this.perPage, this.filters)
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
          this.financeBookManager.getInfoByPageAndFilters(1, this.perPage, this.filters)
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
          // { key: 'actions', label: this.$store.state.t('Actions')},
          { key: 'till', label: this.$store.state.t('Till'), sortable: true},
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
          { key: 'base_document', label: this.$store.state.t('Base Document'), sortable: true},
          { key: 'base_document_content', label: this.$store.state.t('Base Document Content'), sortable: true},
          { key: 'own_company', label: this.$store.state.t('Own Company'), sortable: true},
          { key: 'payment_account', label: this.$store.state.t('Payment Account'), sortable: true},

          { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
          { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
          { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
          { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
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

        // this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    }
  }
</script>