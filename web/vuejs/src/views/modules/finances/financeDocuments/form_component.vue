<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >
          <v-text-field
                  v-model="document_code"
                  :error-messages="document_codeErrors"
                  :counter="250"
                  :label="$store.state.t('Document Code')"
                  required
                  @input="$v.document_code.$touch()"
                  @blur="$v.document_code.$touch()"
          ></v-text-field>
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
          <v-autocomplete
                  v-model="parent_document_id"
                  :items="parentDocumentItems"
                  item-value="id"
                  item-text="document_code"
                  :placeholder="$store.state.t('Type 3 Symbols Or More')"
                  :label="$store.state.t('Parent Document')"
                  :search-input.sync="term"
                  @keyup="getParentDocuments"
          ></v-autocomplete>
          <v-autocomplete
                  v-model="contractor_id"
                  :error-messages="contractor_idErrors"
                  :items="contractorItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Contractor')"
                  required
                  @input="$v.contractor_id.$touch()"
                  @blur="$v.contractor_id.$touch()"
          ></v-autocomplete>
          <v-autocomplete
                  v-model="country_id"
                  :error-messages="country_idErrors"
                  :items="countryItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Country')"
                  required
                  @input="$v.country_id.$touch()"
                  @blur="$v.country_id.$touch()"
                  @change="onCountryChange"
          ></v-autocomplete>
          <v-select
                  v-model="document_type_id"
                  :error-messages="document_type_idErrors"
                  :items="documentTypeItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Document Type')"
                  required
                  @input="$v.document_type_id.$touch()"
                  @blur="$v.document_type_id.$touch()"
          ></v-select>
          <v-autocomplete
                  v-model="own_company_id"
                  :error-messages="own_company_idErrors"
                  :items="ownCompanyItems"
                  item-value="id"
                  item-text="company"
                  :label="$store.state.t('Own Company')"
                  required
                  @input="$v.own_company_id.$touch()"
                  @blur="$v.own_company_id.$touch()"
          ></v-autocomplete>
          <v-select
                  v-model="document_status_id"
                  :error-messages="document_status_idErrors"
                  :items="documentStatusItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Document Status')"
                  required
                  @input="$v.document_type_id.$touch()"
                  @blur="$v.document_type_id.$touch()"
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
  import {CurrenciesManager} from "../../../../managers/CurrenciesManager";
  import {OwnCompaniesManager} from "../../../../managers/OwnCompaniesManager";
  import {DocumentStatusesManager} from "../../../../managers/DocumentsStatusesManager";
  import {DocumentTypesManager} from "../../../../managers/DocumentTypesManager";
  import {CountriesManager} from "../../../../managers/CountriesManager";
  import {FinanceDocumentsManager} from "../../../../managers/FinanceDocumentsManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {
      document_code: { required },
      date: { required },
      contractor_id: { required },
      country_id: { required },
      currency_id: { required },
      document_type_id: { required },
      own_company_id: { required },
      document_status_id: { required },
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


        document_code: null,
        date: null,
        contractor_id: null,
        currency_id: null,
        country_id: null,
        document_type_id: null,
        parent_document_id: null,
        own_company_id: null,
        document_status_id: '2',
        notice: null,

        term: null,


        contractorItems: [],
        currencyItems: [],
        countryItems: [],
        documentStatusItems: [],
        documentTypeItems: [],
        // financeDocumentContentItems: [],
        ownCompanyItems: [],
        parentDocumentItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.contractorManager = new CM();
      this.countriesManager = new CountriesManager();
      this.currenciesManager = new CurrenciesManager();
      this.documentStatusManager = new DocumentStatusesManager();
      this.documentTypeManager = new DocumentTypesManager();
      // this.financeDocumentsContentManager = new FinanceDocumentsContentManager();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.financeDocumentsManager = new FinanceDocumentsManager();


      this.getContractors();
      this.getCurrencies();
      this.getDocumentsStatuses();
      this.getDocumentTypes();
      // this.getFinanceDocumentContents();
      this.getOwnCompanies();
      this.getCountriesForSelect();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.financeDocumentsManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.document_code = response.data.document_code;
                    this.date = response.data.date;
                    this.contractor_id = response.data.contractor_id;
                    this.currency_id = response.data.currency_id;
                    this.country_id = response.data.country_id;
                    this.document_type_id = response.data.document_type_id;
                    this.parent_document_id = response.data.parent_document_id;
                    this.own_company_id = response.data.own_company_id;
                    this.document_status_id = response.data.document_status_id;
                    this.notice = response.data.notice;

                    this.$nextTick(()=>{
                      this.selectDocumentTypeByCountry();
                      this.getParentDocumentById();
                    });
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
      getParentDocuments: function () {
        if (this.term.length < 3) {
          return;
        }
        this.financeDocumentsManager.getAllByTerm(this.term, this.rowId)
                .then( (response) => {
                  if(response.data !== false){
                    this.parentDocumentItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getCountriesForSelect: function () {
        this.countriesManager.getForSelectAccordingDocumentTypes()
                .then( (response) => {
                  if(response.data !== false){
                    this.countryItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      onCountryChange: function(){
        this.document_type_id = null;
        this.selectDocumentTypeByCountry();
      },
      selectDocumentTypeByCountry: function(){
        this.documentTypeManager.getByCountryId(this.country_id)
            .then( (response) => {
              if(response.data !== false){
                this.documentTypeItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getParentDocumentById: function(){
        this.financeDocumentsManager.getById(this.parent_document_id)
            .then( (response) => {
              if(response.data !== false){
                this.parentDocumentItems.push({id:response.data.id, document_code:response.data.document_code});
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getDocumentTypes: function () {
        this.documentTypeManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.documentTypeItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
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
          document_code: this.document_code,
          date: this.date,
          contractor_id: this.contractor_id,
          currency_id: this.currency_id,
          country_id: this.country_id,
          document_type_id: this.document_type_id,
          parent_document_id: this.parent_document_id,
          own_company_id: this.own_company_id,
          document_status_id: this.document_status_id,
          notice: this.notice
        };

        this.financeDocumentsManager.create(createData)
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
          document_code: this.document_code,
          date: this.date,
          contractor_id: this.contractor_id,
          currency_id: this.currency_id,
          country_id: this.country_id,
          document_type_id: this.document_type_id,
          parent_document_id: this.parent_document_id,
          own_company_id: this.own_company_id,
          document_status_id: this.document_status_id,
          notice: this.notice,
          id: this.rowId
        };

        this.financeDocumentsManager.update(updateData)
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
        this.document_code = null;
        this.date = null;
        this.contractor_id = null;
        this.currency_id = null;
        this.country_id = null;
        this.document_type_id = null;
        this.parent_document_id = null;
        this.own_company_id = null;
        this.document_status_id = '2';
        this.notice = null;
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
      document_codeErrors () {
        const errors = []
        if (!this.$v.document_code.$dirty) return errors
        !this.$v.document_code.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      dateErrors () {
        const errors = []
        if (!this.$v.date.$dirty) return errors
        !this.$v.date.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      contractor_idErrors () {
        const errors = []
        if (!this.$v.contractor_id.$dirty) return errors
        !this.$v.contractor_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      country_idErrors () {
        const errors = []
        if (!this.$v.country_id.$dirty) return errors
        !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      currency_idErrors () {
        const errors = []
        if (!this.$v.currency_id.$dirty) return errors
        !this.$v.currency_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      document_status_idErrors () {
        const errors = []
        if (!this.$v.document_status_id.$dirty) return errors
        !this.$v.document_status_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      document_type_idErrors () {
        const errors = []
        if (!this.$v.document_type_id.$dirty) return errors
        !this.$v.document_type_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      own_company_idErrors () {
        const errors = []
        if (!this.$v.own_company_id.$dirty) return errors
        !this.$v.own_company_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
