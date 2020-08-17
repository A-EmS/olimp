<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >
<!--          <v-autocomplete-->
<!--                  v-model="base_document_id"-->
<!--                  :items="financeDocumentItems"-->
<!--                  item-value="id"-->
<!--                  item-text="name"-->
<!--                  :label="$store.state.t('Base Document')"-->
<!--          ></v-autocomplete>-->
<!--          <v-autocomplete-->
<!--                  v-model="base_document_content_id"-->
<!--                  :items="financeDocumentContentItems"-->
<!--                  item-value="id"-->
<!--                  item-text="name"-->
<!--                  :label="$store.state.t('Base Document Content')"-->
<!--          ></v-autocomplete>-->
<!--          <v-autocomplete-->
<!--                  v-model="own_company_id"-->
<!--                  :error-messages="own_company_idErrors"-->
<!--                  :items="ownCompanyItems"-->
<!--                  item-value="id"-->
<!--                  item-text="company"-->
<!--                  :label="$store.state.t('Own Company')"-->
<!--                  required-->
<!--                  @input="$v.own_company_id.$touch()"-->
<!--                  @blur="$v.own_company_id.$touch()"-->
<!--                  @change="getPaymentAccounts(own_company_id)"-->
<!--          ></v-autocomplete>-->
<!--          <v-autocomplete-->
<!--                  v-model="payment_account_id"-->
<!--                  :error-messages="payment_account_idErrors"-->
<!--                  :items="paymentAccountItems"-->
<!--                  item-value="id"-->
<!--                  item-text="name"-->
<!--                  :label="$store.state.t('Payment Account')"-->
<!--                  required-->
<!--                  @input="$v.payment_account_id.$touch()"-->
<!--                  @blur="$v.payment_account_id.$touch()"-->
<!--          ></v-autocomplete>-->
          <!--          <v-select-->
          <!--                  v-model="finance_action_id"-->
          <!--                  :error-messages="finance_action_idErrors"-->
          <!--                  :items="financeActionItems"-->
          <!--                  item-value="id"-->
          <!--                  item-text="name"-->
          <!--                  :label="$store.state.t('Finance Action')"-->
          <!--                  required-->
          <!--                  @input="$v.finance_action_id.$touch()"-->
          <!--                  @blur="$v.finance_action_id.$touch()"-->
          <!--          ></v-select>-->
          <v-select
                  v-model="payment_operation_type_id"
                  :error-messages="payment_operation_type_idErrors"
                  :items="paymentOperationTypeItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Payment Operation Type')"
                  required
                  @input="$v.payment_operation_type_id.$touch()"
                  @blur="$v.payment_operation_type_id.$touch()"
          ></v-select>
          <!--          <v-select-->
          <!--                  v-model="payment_type_id"-->
          <!--                  :error-messages="payment_type_idErrors"-->
          <!--                  :items="paymentTypeItems"-->
          <!--                  item-value="id"-->
          <!--                  item-text="name"-->
          <!--                  :label="$store.state.t('Payment Type')"-->
          <!--                  required-->
          <!--                  @input="$v.payment_type_id.$touch()"-->
          <!--                  @blur="$v.payment_type_id.$touch()"-->
          <!--          ></v-select>-->
          <v-autocomplete
                  v-model="finance_class_id"
                  :error-messages="finance_class_idErrors"
                  :items="financeClassItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Finance Class')"
                  required
                  @input="$v.finance_class_id.$touch()"
                  @blur="$v.finance_class_id.$touch()"
          ></v-autocomplete>
          <v-autocomplete
                  v-model="contractor_id"
                  :items="contractorItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Contractor')"
          ></v-autocomplete>
          <v-flex xs12 sm12 md12>
            <v-menu
                    v-model="dateMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    min-width="290px"
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                        v-model="date"
                        :error-messages="dateErrors"
                        :label="$store.state.t('Date')"
                        prepend-icon="event"
                        required
                        v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker v-model="date" @input="dateMenu = false"></v-date-picker>
            </v-menu>
          </v-flex>
          <v-flex xs12 sm12 md12>
            <v-menu
                    v-model="reportPeriodMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    lazy
                    transition="scale-transition"
                    offset-y
                    full-width
                    min-width="290px"
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                        v-model="report_period"

                        :label="$store.state.t('Report Period')"
                        prepend-icon="event"
                        required
                        v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker v-model="report_period" @input="reportPeriodMenu = false"></v-date-picker>
            </v-menu>
          </v-flex>
          <v-autocomplete
                  v-model="currency_id"
                  :error-messages="currency_idErrors"
                  :items="currencyItems"
                  item-value="id"
                  item-text="currency_name"
                  :label="$store.state.t('Currency')"
                  required
                  @input="$v.currency_id.$touch()"
                  @blur="$v.currency_id.$touch()"
          ></v-autocomplete>
          <v-text-field
                  v-model="amount"
                  :error-messages="amountErrors"
                  :label="$store.state.t('Amount')"
                  required
                  type="number"
                  min="0"
                  step="0.01"
                  @input="$v.currency_id.$touch()"
                  @blur="$v.currency_id.$touch()"
                  :counter="250"
          ></v-text-field>
          <v-select
                  v-model="document_status_id"
                  :items="documentStatusItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Status')"
                  required
          ></v-select>
          <v-text-field
                  v-model="notice"
                  :label="$store.state.t('Notice')"
                  :counter="250"
          ></v-text-field>

          <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
          <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>

    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../../Layout/Components/DemoCard';

  import { validationMixin } from 'vuelidate'
  import { required } from 'vuelidate/lib/validators'
  import loadercustom from "../../../components/loadercustom";
  import {CM} from "../../../../managers/ContractorsManager";
  import {PaymentOperationTypeManager} from "../../../../managers/PaymentOperationTypeManager";
  // import {PaymentTypeManager} from "../../../../managers/PaymentTypeManager";
  import {FinanceClassesManager} from "../../../../managers/FinanceClassesManager";
  import {CurrenciesManager} from "../../../../managers/CurrenciesManager";
  import {FinanceDocumentsManager} from "../../../../managers/FinanceDocumentsManager";
  // import {OwnCompaniesManager} from "../../../../managers/OwnCompaniesManager";
  // import {PaymentAccountsManager} from "../../../../managers/PaymentAccountsManager";
  // import {FinanceActionsManager} from "../../../../managers/FinanceActionsManager";
  import {FinanceDocumentsContentManager} from "../../../../managers/FinanceDocumentsContentManager";
  import {DocumentStatusesManager} from "../../../../managers/DocumentsStatusesManager";
  import {OrdersManager} from "../../../../managers/OrdersManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {
      payment_operation_type_id: { required },
      // payment_type_id: { required },
      finance_class_id: { required },
      currency_id: { required },
      date: { required },
      // report_period: { required },
      amount: { required },
      // document_status_id: { required },
      // own_company_id: { required },
      // payment_account_id: { required },
      // finance_action_id: { required },
      // base_document_id: { required },
      // base_document_content_id: { required },
    },

    data () {
      return {
        customDialogfrontString: 'Please stand by',
        showCustomLoaderDialog: false,
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        dateMenu: false,
        reportPeriodMenu: false,

        payment_operation_type_id: null,
        payment_type_id: 1,
        finance_class_id: null,
        contractor_id: null,
        date: null,
        report_period: null,
        currency_id: null,
        amount: null,
        document_status_id: null,
        notice: null,
        // base_document_id: null,
        // base_document_content_id: null,
        // own_company_id: null,
        // payment_account_id: null,
        finance_action_id: 2,

        paymentOperationTypeItems: [],
        // paymentTypeItems: [],
        financeClassItems: [],
        contractorItems: [],
        currencyItems: [],
        documentStatusItems: [],
        financeDocumentItems: [],
        financeDocumentContentItems: [],
        // ownCompanyItems: [],
        // paymentAccountItems: [],
        // financeActionItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
      till_id: {type: Number, require: true},
    },
    created() {

      this.ordersManager = new OrdersManager();
      this.paymentOperationTypeManager = new PaymentOperationTypeManager();
      // this.paymentTypeManager = new PaymentTypeManager();
      this.financeClassesManager = new FinanceClassesManager();
      this.contractorManager = new CM();
      this.currenciesManager = new CurrenciesManager();
      this.documentStatusManager = new DocumentStatusesManager();
      this.financeDocumentsManager = new FinanceDocumentsManager();
      this.financeDocumentsContentManager = new FinanceDocumentsContentManager();
      // this.ownCompaniesManager = new OwnCompaniesManager();
      // this.paymentAccountsManager = new PaymentAccountsManager();
      // this.financeActionsManager = new FinanceActionsManager();

      // this.getOwnCompanies();
      // this.getPaymentAccounts();
      // this.getFinanceActions();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.ordersManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.payment_operation_type_id = response.data.payment_operation_type_id;
                    this.finance_class_id = response.data.finance_class_id;
                    this.contractor_id = response.data.contractor_id;
                    this.date = response.data.date;
                    this.report_period = response.data.report_period;
                    this.currency_id = response.data.currency_id;
                    this.amount = response.data.amount;
                    this.notice = response.data.notice;
                    // this.base_document_id = response.data.base_document_id;
                    // this.base_document_content_id = response.data.base_document_content_id;
                    this.document_status_id = response.data.document_status_id;
                    // this.own_company_id = response.data.own_company_id;
                    // this.payment_account_id = response.data.payment_account_id;

                    // this.getPaymentAccounts(response.data.own_company_id);
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.header = this.$store.state.t('Updating')+'...';
        this.showDialog = true;
      });

    },

    methods: {
        initFormComponent: function(){
            this.getPaymentOperationTypes();
            this.getFinanceClasses();
            this.getContractors();
            this.getCurrencies();
            this.getDocumentsStatuses();
            this.getFinanceDocuments();
            this.getFinanceDocumentContents();
        },
      getContractors: function () {
        this.contractorManager.getForProjectSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.contractorItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
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

      getFinanceDocuments: function() {
        this.financeDocumentsManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.financeDocumentItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      getFinanceDocumentContents: function() {
        this.financeDocumentsContentManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.financeDocumentContentItems = response.data.items;
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

      // getPaymentAccounts: function(own_company_id) {
      //   if (own_company_id > 0) {
      //     this.paymentAccountsManager.getAllByOwnCompanyId(own_company_id)
      //             .then( (response) => {
      //               if(response.data !== false){
      //                 this.paymentAccountItems = response.data.items;
      //               }
      //             })
      //             .catch(function (error) {
      //               console.log(error);
      //             });
      //   }
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

      submit: function () {
        this.$v.$touch();
        if (!this.$v.$invalid) {
          if (this.rowId === 0){
            this.create();
          } else {
            this.update();
          }
        }
      },
      create: function(){

        var createData = {
          till_id: this.till_id,
          payment_operation_type_id: this.payment_operation_type_id,
          payment_type_id: this.payment_type_id,
          finance_class_id: this.finance_class_id,
          contractor_id: this.contractor_id,
          date: this.date,
          report_period: this.report_period,
          currency_id: this.currency_id,
          amount: this.amount,
          document_status_id: this.document_status_id,
          notice: this.notice,
          // base_document_id: this.base_document_id,
          // base_document_content_id: this.base_document_content_id,
          // own_company_id: this.own_company_id,
          // payment_account_id: this.payment_account_id,
          finance_action_id: this.finance_action_id
        };

        this.ordersManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
                    } else {
                      this.openErrorDialog(response.data.error);
                    }
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          till_id: this.till_id,
          payment_operation_type_id: this.payment_operation_type_id,
          payment_type_id: this.payment_type_id,
          finance_class_id: this.finance_class_id,
          contractor_id: this.contractor_id,
          date: this.date,
          report_period: this.report_period,
          currency_id: this.currency_id,
          amount: this.amount,
          document_status_id: this.document_status_id,
          notice: this.notice,
          // base_document_id: this.base_document_id,
          // base_document_content_id: this.base_document_content_id,
          // own_company_id: this.own_company_id,
          // payment_account_id: this.payment_account_id,
          finance_action_id: this.finance_action_id,
          id: this.rowId
        };

        this.ordersManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
                    } else {
                      this.openErrorDialog(response.data.error);
                    }
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      cancel () {
        this.$v.$reset();
        this.showDialog = false;
      },

      setDefaultData () {
        this.payment_operation_type_id = null;
        this.payment_type_id = 1;
        this.finance_class_id = null;
        this.contractor_id = null;
        this.date = null;
        this.report_period = null;
        this.currency_id = null;
        this.amount = null;
        this.document_status_id = '2';
        this.notice = null;
        // this.base_document_id = null;
        // this.base_document_content_id = null;
        // this.own_company_id = null;
        // this.payment_account_id = null;
        this.finance_action_id = 2;
        this.rowId = 0;
      },

      openErrorDialog(message, time){
        var dialogTime = time || 5000;
        this.customDialogfrontString = this.$store.state.t(message);
        this.showCustomLoaderDialog = true;
        setTimeout(() => {
          this.showCustomLoaderDialog = false;
        }, dialogTime);
      },
    },
    watch: {

    },
    computed: {
      payment_operation_type_idErrors () {
        const errors = []
        if (!this.$v.payment_operation_type_id.$dirty) return errors
        !this.$v.payment_operation_type_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      payment_type_idErrors () {
        const errors = []
        if (!this.$v.payment_type_id.$dirty) return errors
        !this.$v.payment_type_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      currency_idErrors () {
        const errors = []
        if (!this.$v.currency_id.$dirty) return errors
        !this.$v.currency_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      finance_class_idErrors () {
        const errors = []
        if (!this.$v.finance_class_id.$dirty) return errors
        !this.$v.finance_class_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      document_status_idErrors () {
        const errors = []
        if (!this.$v.document_status_id.$dirty) return errors
        !this.$v.document_status_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      // own_company_idErrors () {
      //   const errors = []
      //   if (!this.$v.own_company_id.$dirty) return errors
      //   !this.$v.own_company_id.required && errors.push(this.$store.state.t('Required field'))
      //   return errors
      // },
      // payment_account_idErrors () {
      //   const errors = []
      //   if (!this.$v.payment_account_id.$dirty) return errors
      //   !this.$v.payment_account_id.required && errors.push(this.$store.state.t('Required field'))
      //   return errors
      // },
      amountErrors () {
        const errors = []
        if (!this.$v.amount.$dirty) return errors
        !this.$v.amount.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      finance_action_idErrors () {
        const errors = []
        if (!this.$v.finance_action_id.$dirty) return errors
        !this.$v.finance_action_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      dateErrors () {
        const errors = []
        if (!this.$v.date.$dirty) return errors
        !this.$v.date.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      report_periodErrors () {
        const errors = []
        if (!this.$v.report_period.$dirty) return errors
        !this.$v.date.report_period && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
