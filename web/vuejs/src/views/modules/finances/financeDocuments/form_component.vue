<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >
          <b-row>
            <b-col md="12">
              <b-card class="mb-6 nav-justified" no-body>
                <b-tabs v-model="tabIndex" card>
                  <b-tab :title="$store.state.t('General')">
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
                      <v-autocomplete
                              v-if="contractorInputId <= 0"
                              :readonly="contractorInputId > 0"
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
                      <v-select
                              v-model="document_type_id"
                              :error-messages="document_type_idErrors"
                              :items="documentTypeItems"
                              :disabled="country_id <= 0"
                              item-value="id"
                              item-text="name"
                              :label="$store.state.t('Document Type')"
                              required
                              @input="$v.document_type_id.$touch()"
                              @blur="$v.document_type_id.$touch()"
                              @change="onDocumentTypeChange"
                      ></v-select>
                      <v-autocomplete
                              v-if="currentDocumentTypeScenario == constants.documentScenarioIdContract"
                              v-model="individual_id_manager"
                              :items="individualsManagerItems"
                              item-value="id"
                              item-text="full_name"
                              :placeholder="$store.state.t('Type 3 Symbols Or More')"
                              :label="$store.state.t('Manager')"
                              :search-input.sync="term"
                              @keyup="getIndividualsByTerm"
                      ></v-autocomplete>
                      <div v-if="currentDocumentTypeScenario == constants.documentScenarioIdContract" class="alert alert-warning">{{$store.state.t('Document Type Contract: it does not provide a parent document')}}</div>
                      <div v-if="currentDocumentTypeScenario == constants.documentScenarioIdAnnex" class="alert alert-warning">{{$store.state.t('Document Type Annex: it provides CONTRACT like parent document')}}</div>
                      <div v-if="currentDocumentTypeScenario == constants.documentScenarioIdAddAgreement" class="alert alert-warning">{{$store.state.t('Document Type Additional Agreement: it provides CONTRACT like parent document')}}</div>
                      <div v-if="currentDocumentTypeScenario == constants.documentScenarioIdAccount" class="alert alert-warning">{{$store.state.t('Document Type Account: it provides CONTRACT and ANNEX like parent documents')}}</div>
                      <div v-if="currentDocumentTypeScenario == constants.documentScenarioIdAct" class="alert alert-warning">{{$store.state.t('Document Type Act: it provides CONTRACT and ANNEX like parent documents')}}</div>
                      <v-text-field
                              v-if="currentDocumentTypeScenario == constants.documentScenarioIdAccount"
                              v-model="percent"
                              :error-messages="percentErrors"
                              :counter="250"
                              :label="$store.state.t('Percent')"
                              required
                              type="number"
                              min="0.001"
                              step="0.001"
                              @input="$v.percent.$touch()"
                              @blur="$v.percent.$touch()"
                              @keyup="onChangePercent"
                      ></v-text-field>
                      <v-autocomplete
                              v-model="parent_document_id"
                              :error-messages="parent_document_idErrors"
                              :disabled="currentDocumentTypeScenario == constants.documentScenarioIdContract || currentDocumentTypeScenario === null"
                              :readonly="currentDocumentTypeScenario != constants.documentScenarioIdAnnex && rowId > 0"
                              :items="parentDocumentItems"
                              item-value="id"
                              item-text="document_code"
                              :label="$store.state.t('Parent Document')"
                              @change="onParentDocumentChange()"
                      ></v-autocomplete>
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
                          <v-date-picker :min=miDatePickerValue v-model="date" @input="dateMenu = false"></v-date-picker>
                        </v-menu>
                      </v-flex>
                      <v-autocomplete
                              v-if="currentDocumentTypeScenario == constants.documentScenarioIdContract"
                              :readonly="currentDocumentTypeScenario == constants.documentScenarioIdContract && documentContentLength > 0"
                              v-model="currency_id"
                              :error-messages="currency_idErrors"
                              :items="currencyItems"
                              item-value="id"
                              item-text="currency_name"
                              :label="$store.state.t('Currency')"
                      ></v-autocomplete>
                      <v-autocomplete
                              v-model="own_company_id"
                              :error-messages="own_company_idErrors"
                              :readonly=isOwnCompanyReadOnly()
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
                  </b-tab>
                  <b-tab v-if="parseInt(currentDocumentTypeScenario) != constants.documentScenarioIdAddAgreement && parseInt(rowId) > 0" :title="$store.state.t('Finance Content')">
                    <div v-if="parseInt(rowId) > 0">
                      <finance-documents-content
                              :key="rowId"
                              :current_document_type_scenario="parseInt(currentDocumentTypeScenario)"
                              :document_id="parseInt(rowId)"
                              @documentContentLoaded="contentLoaded($event)"
                      ></finance-documents-content>
                        <v-btn  @click="cancel">{{$store.state.t('To List')}}</v-btn>
                    </div>
                  </b-tab>
                </b-tabs>
              </b-card>
            </b-col>
          </b-row>
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
  import {minValue, required} from 'vuelidate/lib/validators'
  import loadercustom from "../../../components/loadercustom";
  import {CM} from "../../../../managers/ContractorsManager";
  import {CurrenciesManager} from "../../../../managers/CurrenciesManager";
  import {OwnCompaniesManager} from "../../../../managers/OwnCompaniesManager";
  import {DocumentStatusesManager} from "../../../../managers/DocumentsStatusesManager";
  import {DocumentTypesManager} from "../../../../managers/DocumentTypesManager";
  import {CountriesManager} from "../../../../managers/CountriesManager";
  import {FinanceDocumentsManager} from "../../../../managers/FinanceDocumentsManager";
  import FinanceDocumentsContent from "./FinanceDocumentsContent";
  import constantsMixin from "../../../../mixins/constantsMixin";
  import {IM} from "../../../../managers/IndividualsManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
      FinanceDocumentsContent
    },

    mixins: [validationMixin, constantsMixin],

    validations: {
      document_code: { required },
      date: { required },
      contractor_id: { required },
      country_id: { required },
      document_type_id: { required },
      own_company_id: { required },
      document_status_id: { required },
      percent: { minValue: minValue(0.001) },
    },

    data () {
      return {
        customDialogfrontString: 'Please stand by',
        showCustomLoaderDialog: false,
        showDialog: false,
        valid: true,
        header: '',
        tabIndex: 0,
        rowId: 0,
        dateMenu: false,
        miDatePickerValue: '',

        percent: null,


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

        currentDocumentTypeScenario: null,

        individual_id_manager: 0,
        individualsManagerItems: [],
        term: '',

        documentContentLength: 0,

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
      contractorInputId: {type: Number, require: false, default: 0},
    },
    created() {
      this.individualsManager = new IM();
      this.contractorManager = new CM();
      this.countriesManager = new CountriesManager();
      this.currenciesManager = new CurrenciesManager();
      this.documentStatusManager = new DocumentStatusesManager();
      this.documentTypeManager = new DocumentTypesManager();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.financeDocumentsManager = new FinanceDocumentsManager();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.initFormComponent();
        this.setDefaultData();
        this.showDialog = true;
        if (this.contractorInputId > 0) {
           this.contractor_id = this.contractorInputId.toString();
          this.getCountryByContractorId();
          this.getManagerByContractorId();
        }
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.rowId = 0;
        this.financeDocumentsManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.document_code = response.data.document_code;
                    this.date = response.data.date;
                    this.contractor_id = response.data.contractor_id;
                    this.currency_id = response.data.currency_id;
                    this.country_id = response.data.country_id;
                    this.percent = response.data.percent;
                    this.document_type_id = response.data.document_type_id;
                    this.parent_document_id = response.data.parent_document_id;
                    this.own_company_id = response.data.own_company_id;
                    this.document_status_id = response.data.document_status_id;
                    this.notice = response.data.notice;

                    if (response.data.individual_id_manager > 0) {
                      this.individual_id_manager = response.data.individual_id_manager;
                      this.individualsManagerItems = [{id: response.data.individual_id_manager, full_name: response.data.individual_id_manager_full_name}];
                    } else if (response.data.contractor_individual_id_manager > 0) {
                        this.individual_id_manager = response.data.contractor_individual_id_manager;
                        this.individualsManagerItems = [{id: response.data.contractor_individual_id_manager, full_name: response.data.contractor_individual_id_manager_full_name}];
                    }

                    this.currentDocumentTypeScenario = response.data.scenario_type;

                    this.header = this.$store.state.t('Updating')+'...'+this.document_code;
                    this.$nextTick(()=>{
                      this.selectDocumentTypeByCountry();
                      this.getParentDocumentById().then(() => { this.onParentDocumentChange() });
                    });
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.showDialog = true;
      });

    },

    methods: {
        initFormComponent: function(){
            this.getContractors();
            this.getCurrencies();
            this.getDocumentsStatuses();
            this.getDocumentTypes();
            this.getOwnCompanies();
            this.getCountriesForSelect();
        },
        isOwnCompanyReadOnly: function (){
          return this.parent_document_id > 0 || this.currentDocumentTypeScenario == this.constants.documentScenarioIdContract && this.documentContentLength > 0;
        },
        onParentDocumentChange: function () {
          var parentDocument = this.parentDocumentItems.find(doc => parseInt(doc.id) === parseInt(this.parent_document_id));
          if (typeof parentDocument !== 'undefined') {
            this.miDatePickerValue = parentDocument.document_date;
            this.own_company_id = parentDocument.document_own_company_id;
          }
        },
        contentLoaded: function (data) {
          this.documentContentLength = data.contentCount;
        },
        getIndividualsByTerm: function (){
            if (this.term === null || (this.term !== null && this.term.length < 3)) {
                return;
            }
            this.individualsManager.getAllByTerm(this.term)
                .then( (response) => {
                    if(response.data !== false){
                        this.individualsManagerItems = response.data.items;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
      getParentDocuments: function () {
        this.financeDocumentsManager.getAllByContractor(this.contractor_id, this.rowId, this.currentDocumentTypeScenario, this.country_id)
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
        this.currentDocumentTypeScenario = null;
        this.selectDocumentTypeByCountry();
      },
      onDocumentTypeChange: function(){
        var docTypeFiltered = this.documentTypeItems.filter(obj => parseInt(obj.id) === parseInt(this.document_type_id));
        var docType = docTypeFiltered.pop();

        this.currentDocumentTypeScenario = docType.scenario_type;
        this.parent_document_id = null;
        this.parentDocumentItems = [];
        if (this.currentDocumentTypeScenario != this.constants.documentScenarioIdContract) {
          this.currency_id = null;
        }

        this.percent = null;

        this.getParentDocuments();
      },
        onChangePercent: function(){
            if (this.percent > 100) {
                this.percent = 100;
            } else if (this.percent < 0) {
                this.percent = 0;
            }
        },
      getCountryByContractorId: function (){
        this.countriesManager.getByContractorId(this.contractor_id)
            .then( (response) => {
              if(response.data !== false){
                this.country_id = response.data.country_id.toString();
                this.currency_id = response.data.currency_id.toString();
                this.onCountryChange();
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getManagerByContractorId: function(){
            if (this.individualsManagerItems.length > 0){
                return;
            }
          this.contractorManager.getManagerByContractorId(this.contractor_id)
              .then( (response) => {
                  if(response.data !== false){
                      this.individual_id_manager = response.data.item.individual_id_manager;
                      this.individualsManagerItems = [{id: response.data.item.individual_id_manager, full_name: response.data.item.individual_id_manager_full_name}];
                  }
              })
              .catch(function (error) {
                  console.log(error);
              });
      },
      selectDocumentTypeByCountry: function(){

        if (this.country_id <= 0) {
          return;
        }
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
        if(this.parent_document_id === null) {
            return Promise.resolve();
        }
        return this.financeDocumentsManager.getById(this.parent_document_id)
            .then( (response) => {
              if(response.data !== false){
                this.parentDocumentItems.push(
                      {
                        id:response.data.id,
                        document_code:response.data.document_code,
                        document_date:response.data.date,
                        document_own_company_id:response.data.own_company_id
                      }
                    );
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
        this.ownCompaniesManager.getAllWithUsersCompanies()
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
        if (!this.$v.$invalid &&
            ((this.currentDocumentTypeScenario == this.constants.documentScenarioIdContract && this.parent_document_id == null) || (this.currentDocumentTypeScenario != this.constants.documentScenarioIdContract  && this.parent_document_id != null)) &&
            ((this.currentDocumentTypeScenario == this.constants.documentScenarioIdAccount && this.percent != null) || (this.currentDocumentTypeScenario != this.constants.documentScenarioIdAccount  && this.percent == null)) &&
            ((this.currentDocumentTypeScenario == this.constants.documentScenarioIdContract && this.currency_id != null) || (this.currentDocumentTypeScenario != this.constants.documentScenarioIdContract  && this.currency_id == null))
        ) {
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
          percent: this.percent,
          country_id: this.country_id,
          document_type_id: this.document_type_id,
          parent_document_id: this.parent_document_id,
          own_company_id: this.own_company_id,
          document_status_id: this.document_status_id,
          individual_id_manager: this.individual_id_manager,
          notice: this.notice
        };

        this.financeDocumentsManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                        window.j('html, body').animate({scrollTop: 0}, 400);
                        this.rowId = response.data.id;
                        this.currentDocumentTypeScenario = response.data.scenario_type;
                    } else {
                      this.openErrorDialog(response.data.error);
                    }
                  }
                }).then(()=>{
                    if (this.currentDocumentTypeScenario != this.constants.documentScenarioIdAddAgreement) {
                        this.$nextTick(()=>{
                            this.tabIndex++;
                        });
                    } else {
                        this.showDialog = false;
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
          percent: this.percent,
          country_id: this.country_id,
          document_type_id: this.document_type_id,
          parent_document_id: this.parent_document_id,
          own_company_id: this.own_company_id,
          document_status_id: this.document_status_id,
          notice: this.notice,
          individual_id_manager: this.individual_id_manager,
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
        this.percent = null;
        this.country_id = null;
        this.document_type_id = null;
        this.parent_document_id = null;
        this.own_company_id = null;
        this.document_status_id = '2';
        this.notice = null;
        this.individual_id_manager = 0;
        this.individualsManagerItems = [];
        this.term = '';
        this.documentContentLength = 0;
        this.miDatePickerValue = '';
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
        if (this.currentDocumentTypeScenario != this.constants.documentScenarioIdContract) {
          return errors;
        }

        errors.push(this.$store.state.t('Required field'))
        return errors
      },
      percentErrors () {
        const errors = []
        !this.$v.percent.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.001'))
        if (this.currentDocumentTypeScenario != this.constants.documentScenarioIdAccount || this.percent !== null) {
          return errors;
        }

        errors.push(this.$store.state.t('Required field'))
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
      parent_document_idErrors () {
        const errors = []
        if (this.currentDocumentTypeScenario == this.constants.documentScenarioIdContract || this.currentDocumentTypeScenario == null || (this.currentDocumentTypeScenario > this.constants.documentScenarioIdContract && this.parent_document_id > 0)) {
          return errors;
        }

        errors.push(this.$store.state.t('Required field'))
        // if (!this.$v.parent_document_id.$dirty) return errors
        // !this.$v.parent_document_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
