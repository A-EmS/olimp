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
                  <b-tab :title="name">
                    <v-text-field
                        v-model="name"
                        :error-messages="nameErrors"
                        :counter="250"
                        :label="$store.state.t('Name')"
                        required
                        @input="$v.name.$touch()"
                        @blur="$v.name.$touch()"
                    ></v-text-field>
                    <v-autocomplete
                        v-model="country_id"
                        :error-messages="country_idErrors"
                        :items="countryItems"
                        item-value="id"
                        item-text="name"
                        :label="$store.state.t('Country')"
                        :disabled="rowId > 0"
                        required
                        @input="$v.country_id.$touch()"
                        @blur="$v.country_id.$touch()"
                        @change="onCountryChange()"
                    ></v-autocomplete>
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
                    <v-autocomplete
                        v-model="request_manager_individual_id"
                        :error-messages="request_manager_individual_idErrors"
                        :items="individualItems"
                        item-value="id"
                        item-text="name"
                        :label="$store.state.t('Requester Manager')"
                        required
                        @input="$v.request_manager_individual_id.$touch()"
                        @blur="$v.request_manager_individual_id.$touch()"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="contractor_id"
                            :items="contractorItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Contractor')"
                    ></v-autocomplete>
                    <v-autocomplete
                        v-model="construction_type_id"
                        :error-messages="construction_type_idErrors"
                        :items="construction_typeItems"
                        item-value="id"
                        item-text="name"
                        :label="$store.state.t('Construction Type')"
                        :disabled="country_id <= 0"
                        required
                        @input="$v.construction_type_id.$touch()"
                        @blur="$v.construction_type_id.$touch()"
                    ></v-autocomplete>
                    <v-textarea
                        v-model="description"
                        :error-messages="descriptionErrors"
                        :label="$store.state.t('Description')"
                        required
                        @input="$v.own_company_id.$touch()"
                        @blur="$v.own_company_id.$touch()"
                    ></v-textarea>
                    <v-textarea
                        v-model="customer_provide"
                        :label="$store.state.t('Customer Provide')"
                        required
                    ></v-textarea>
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
                        v-model="project_status_id"
                        :error-messages="project_status_idErrors"
                        :items="project_statusItems"
                        item-value="id"
                        item-text="status"
                        :label="$store.state.t('Project Status')"
                        required
                        @input="$v.project_status_id.$touch()"
                        @blur="$v.project_status_id.$touch()"
                    ></v-autocomplete>
                    <v-text-field
                            v-model="notice"
                            :label="$store.state.t('Notice')"
                            :counter="250"
                    ></v-text-field>

                    <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                    <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
                  </b-tab>
                  <b-tab v-if="parseInt(rowId) > 0" :title="$store.state.t('Project Stages')">
                    <request-project-stages
                        :key="rowId"
                        :request_id="parseInt(rowId)"
                        :country_id="parseInt(this.country_id)"
                        :own_company_id="parseInt(this.own_company_id)"
                        :updateTabsTrigger="updateTabsTrigger"
                    ></request-project-stages>
                    <v-btn  @click="cancel">{{$store.state.t('To List')}}</v-btn>
                  </b-tab>
                  <b-tab v-if="parseInt(rowId) > 0" :title="$store.state.t('Request Labor Costs')">
                    <request-labor-cost
                        :key="rowId"
                        :request_id="parseInt(rowId)"
                        :country_id="parseInt(this.country_id)"
                        :own_company_id="parseInt(this.own_company_id)"
                        :updateCustomEventName="updateItemListNameTrigger"
                        :updateTabsTrigger="updateTabsTrigger"
                    ></request-labor-cost>
                      <v-btn  @click="cancel">{{$store.state.t('To List')}}</v-btn>
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
  import { required } from 'vuelidate/lib/validators'
  import loadercustom from "../../../components/loadercustom";
  import {CM} from "@/managers/ContractorsManager";
  import {OwnCompaniesManager} from "@/managers/OwnCompaniesManager";
  import {RequestsManager} from "@/managers/RequestsManager";
  import qs from "qs";
  import {CountriesManager} from "@/managers/CountriesManager";
  import {IM} from "@/managers/IndividualsManager";
  import {ProjectStatusesManager} from "@/managers/ProjectStatusesManager";
  import {ConstructionTypesManager} from "@/managers/ConstructionTypesManager";
  import RequestLaborCost from "@/views/modules/engineering/requests/RequestLaborCost";
  import RequestProjectStages from "@/views/modules/engineering/requests/RequestProjectStages";

  export default {
    components: {
      RequestProjectStages,
      RequestLaborCost,
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {
      request_manager_individual_id: { required },
      name: { required },
      description: { required },
      date: { required },
      own_company_id: { required },
      country_id: { required },
      project_status_id: { required },
      construction_type_id: { required },
    },

    data () {
      return {
        customDialogfrontString: 'Please stand by',
        showCustomLoaderDialog: false,
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        tabIndex: 0,
        dateMenu: false,
        reportPeriodMenu: false,

        request_manager_individual_id: null,
        name: null,
        contractor_id: null,
        date: null,
        notice: null,
        own_company_id: null,
        construction_type_id: null,
        country_id: null,
        project_status_id: '3',
        description: null,
        customer_provide: null,

        countryItems: [],
        construction_typeItems: [],
        contractorItems: [],
        ownCompanyItems: [],
        individualItems: [],
        project_statusItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
      updateTabsTrigger: {type: String, require: false},
    },
    created() {

      this.contractorManager = new CM();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.requestsManager = new RequestsManager();
      this.countriesManager = new CountriesManager();
      this.individualsManager = new IM();
      this.projectStatusesManager = new ProjectStatusesManager();
      this.constructionTypesManager = new ConstructionTypesManager();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.tabIndex = 0;
        this.showCustomLoaderDialog = true;
        this.requestsManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.country_id = response.data.country_id;
                    this.request_manager_individual_id = response.data.request_manager_individual_id;
                    this.contractor_id = response.data.contractor_id;
                    this.construction_type_id = response.data.construction_type_id;
                    this.description = response.data.description;
                    this.customer_provide = response.data.customer_provide;
                    this.project_status_id = response.data.project_status_id;
                    this.date = response.data.date;
                    this.own_company_id = response.data.own_company_id;
                    this.notice = response.data.notice;

                    setTimeout(() => {this.onCountryChange()}, 1000)
                  }
                  this.showCustomLoaderDialog = false;
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
        this.getContractors();
        this.getOwnCompanies();
        this.getCountriesForSelect();
        this.getIndividuals();
        this.getProjectStatuses();
      },
      onCountryChange: function() {
        this.constructionTypesManager.getAllByCountry(this.country_id)
            .then( (response) => {
              if(response.data !== false){
                this.construction_typeItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },

      getProjectStatuses: function(){
        this.projectStatusesManager.getAll()
            .then( (response) => {
              if(response.data !== false){
                this.project_statusItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getIndividuals: function () {
        this.individualsManager.getForSelect()
            .then( (response) => {
              if(response.data !== false){
                this.individualItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getCountriesForSelect: function () {
        this.countriesManager.getForSelectAccordingProjectParts()
            .then( (response) => {
              if(response.data !== false){
                this.countryItems = response.data.items;
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
          name: this.name,
          country_id: this.country_id,
          request_manager_individual_id: this.request_manager_individual_id,
          contractor_id: this.contractor_id,
          construction_type_id: this.construction_type_id,
          description: this.description,
          customer_provide: this.customer_provide,
          project_status_id: this.project_status_id,
          date: this.date,
          notice: this.notice,
          own_company_id: this.own_company_id
        };

        this.requestsManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      window.j('html, body').animate({scrollTop: 0}, 400);
                      this.rowId = response.data;
                    } else {
                      this.openErrorDialog(response.data.error);
                    }
                  }
                })
                .then(()=>{
                  this.$nextTick(()=>{
                    this.tabIndex = 2;
                  });
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          name: this.name,
          country_id: this.country_id,
          request_manager_individual_id: this.request_manager_individual_id,
          contractor_id: this.contractor_id,
          construction_type_id: this.construction_type_id,
          description: this.description,
          customer_provide: this.customer_provide,
          project_status_id: this.project_status_id,
          date: this.date,
          notice: this.notice,
          own_company_id: this.own_company_id,
          id: this.rowId
        };

        this.requestsManager.update(updateData)
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
        this.contractor_id = null;
        this.project_status_id = '3';
        this.date = null;
        this.name = null;
        this.notice = null;
        this.country_id = null;
        this.construction_type_id = null;
        this.description = null;
        this.customer_provide = null;
        this.own_company_id = null;
        this.request_manager_individual_id = null;
        this.rowId = 0;
        this.tabIndex = 0;
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

    computed: {
      nameErrors () {
        const errors = [];
        if (!this.$v.name.$dirty) return errors;
        !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      descriptionErrors () {
        const errors = [];
        if (!this.$v.description.$dirty) return errors;
        !this.$v.description.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      construction_type_idErrors () {
        const errors = [];
        if (!this.$v.construction_type_id.$dirty) return errors;
        !this.$v.construction_type_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      own_company_idErrors () {
        const errors = []
        if (!this.$v.own_company_id.$dirty) return errors
        !this.$v.own_company_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      dateErrors () {
        const errors = []
        if (!this.$v.date.$dirty) return errors
        !this.$v.date.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      country_idErrors () {
        const errors = []
        if (!this.$v.country_id.$dirty) return errors
        !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      request_manager_individual_idErrors () {
        const errors = []
        if (!this.$v.request_manager_individual_id.$dirty) return errors
        !this.$v.request_manager_individual_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      project_status_idErrors () {
        const errors = []
        if (!this.$v.project_status_id.$dirty) return errors
        !this.$v.project_status_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
