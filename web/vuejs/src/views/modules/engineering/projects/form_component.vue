<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">

        <b-row>
          <b-col md="12">
            <b-card class="mb-6 nav-justified" no-body>
              <b-tabs card>
                <b-tab :title="$store.state.t('Project Info')" active>
                  <v-form
                          ref="form"
                          v-model="valid"
                          lazy-validation
                  >
                    <v-autocomplete
                            v-model="country_id"
                            :error-messages="country_idErrors"
                            :items="countryItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Country')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.country_id.$touch()"
                            @blur="$v.country_id.$touch()"
                            @change="onCountryChange"
                    ></v-autocomplete>

                    <v-autocomplete
                            v-model="status_id"
                            :error-messages="status_idErrors"
                            :items="statusItems"
                            item-value="id"
                            item-text="status"
                            :label="$store.state.t('Status')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.status_id.$touch()"
                            @blur="$v.status_id.$touch()"
                    ></v-autocomplete>

                    <v-text-field
                            v-model="object_crypt"
                            :error-messages="object_cryptErrors"
                            :counter="250"
                            :label="$store.state.t('Object Crypt')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.object_crypt.$touch()"
                            @blur="$v.object_crypt.$touch()"
                    ></v-text-field>
                    <v-text-field
                            v-model="name"
                            :error-messages="nameErrors"
                            :counter="250"
                            :label="$store.state.t('Object Name')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.name.$touch()"
                            @blur="$v.name.$touch()"
                    ></v-text-field>
                    <v-text-field
                            v-model="object_name"
                            :error-messages="object_nameErrors"
                            :counter="500"
                            :label="$store.state.t('Object Name Original')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.object_name.$touch()"
                            @blur="$v.object_name.$touch()"
                    ></v-text-field>
                    <v-text-field
                            v-model="stamp"
                            :error-messages="stampErrors"
                            :counter="500"
                            :label="$store.state.t('Stamp')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.stamp.$touch()"
                            @blur="$v.stamp.$touch()"
                    ></v-text-field>
                    <v-autocomplete
                            v-model="performer_own_company_id"
                            :error-messages="performer_own_company_idErrors"
                            :items="ownCompaniesItems"
                            item-value="id"
                            item-text="company"
                            :label="$store.state.t('Performer')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.performer_own_company_id.$touch()"
                            @blur="$v.performer_own_company_id.$touch()"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="customer_contractor_id"
                            :error-messages="customer_contractor_idErrors"
                            :items="contractor_Items"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Customer')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.customer_contractor_id.$touch()"
                            @blur="$v.customer_contractor_id.$touch()"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="payer_contractor_id"
                            :error-messages="payer_contractor_idErrors"
                            :items="contractor_Items"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Payer')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.payer_contractor_id.$touch()"
                            @blur="$v.payer_contractor_id.$touch()"
                            @change="onChangePayer"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="payer_manager_individual_id"
                            :items="individualItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Payer Manager')"
                            :readonly="!this.ACL.update"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="project_manager_individual_id"
                            :items="individualItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Project Manager')"
                            :readonly="!this.ACL.update"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="finance_document_id"
                            :items="financeDocumentItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Finance Document Basement')"
                            @change="onChangeFinanceDocument"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="finance_document_content_id"
                            :items="financeDocumentContentItems"
                            item-value="id"
                            item-text="name"
                            :placeholder="$store.state.t('Finance Document Content Basement')"
                            :label="$store.state.t('Finance Document Content Basement')"
                    ></v-autocomplete>
                    <v-text-field
                            v-model="archive"
                            :error-messages="archiveErrors"
                            :counter="250"
                            :label="$store.state.t('Archive')"
                            :readonly="!this.ACL.update"
                            required
                            @input="$v.archive.$touch()"
                            @blur="$v.archive.$touch()"
                    ></v-text-field>
                    <v-text-field
                            v-model="notice"
                            :counter="250"
                            :label="$store.state.t('Notice')"
                            :readonly="!this.ACL.update"
                    ></v-text-field>
                    <v-btn v-if="this.ACL.update" color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                    <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
                  </v-form>
                </b-tab>
                <b-tab v-if="this.rowId > 0" :title="$store.state.t('Project Data')">
                  <ProjectData v-if="this.rowId > 0" :projectId="this.rowId" :country_id="this.country_id" :projectObjectName="this.object_name"></ProjectData>
                  <v-btn  @click="cancel">{{$store.state.t('Back')}}</v-btn>
                </b-tab>
                <b-tab v-if="this.rowId > 0" :title="$store.state.t('Project Contacts')">
                  <ProjectContacts v-if="this.rowId > 0" :projectId="this.rowId" :projectObjectName="this.object_name"></ProjectContacts>
                  <v-btn  @click="cancel">{{$store.state.t('Back')}}</v-btn>
                </b-tab>
              </b-tabs>
            </b-card>
          </b-col>
        </b-row>
      </demo-card>
    </layout-wrapper>

    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
    <contactsinfo></contactsinfo>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../../Layout/Components/DemoCard';

  import { validationMixin } from 'vuelidate'
  import { required, maxLength } from 'vuelidate/lib/validators'
  import qs from "qs";
  import {CountriesManager} from "../../../../managers/CountriesManager";
  import {ProjectsManager} from "../../../../managers/ProjectsManager";
  import {IM} from "../../../../managers/IndividualsManager";
  import {CM} from "../../../../managers/ContractorsManager";
  import {OwnCompaniesManager} from "../../../../managers/OwnCompaniesManager";
  import ProjectData from "./ProjectData";
  import loadercustom from "../../../components/loadercustom";
  import ProjectContacts from "./ProjectContacts";
  import contactsinfo from "../../../components/contactsinfo";
  import {FinanceDocumentsManager} from "../../../../managers/FinanceDocumentsManager";
  import {FinanceDocumentsContentManager} from "../../../../managers/FinanceDocumentsContentManager";
  import {ProjectStatusesManager} from "../../../../managers/ProjectStatusesManager";

  export default {
    components: {
      ProjectContacts,
      ProjectData,
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
      contactsinfo
    },

    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(250) },
      country_id: { required },
      status_id: { required },
      object_crypt: { required },
      object_name: { required },
      stamp: { required },
      performer_own_company_id: { required },
      customer_contractor_id: { required },
      payer_contractor_id: { required },
      archive: { required },
    },

    data () {
      return {
        customDialogfrontString: 'Please stand by',
        showCustomLoaderDialog: false,
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,

        object_crypt: '',
        name: '',
        object_name: '',
        stamp: '',

        performer_own_company_id: null,
        customer_contractor_id: null,
        payer_contractor_id: null,
        payer_manager_individual_id: null,
        project_manager_individual_id: null,
        archive: '',
        notice: '',

        finance_document_id: 0,
        finance_document_content_id: 0,
        financeDocumentItems: [],
        financeDocumentContentItems: [],

        country_id: null,
        countryItems: [],

        status_id: 0,
        statusItems: [],

        individualItems: [],
        ownCompaniesItems: [],
        contractor_Items: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
      ACL: {type: Object, require: true},
    },
    created() {

      this.countriesManager = new CountriesManager();
      this.projectsManager = new ProjectsManager();
      this.individualsManager = new IM();
      this.contractorManager = new CM();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.financeDocumentsManager = new FinanceDocumentsManager();
      this.financeDocumentsContentManager = new FinanceDocumentsContentManager();
      this.projectStatusesManager = new ProjectStatusesManager();

      this.getCountriesForSelect();
      this.getIndividuals();
      this.getContractors();
      this.getOwnCompanies();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.projectsManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.country_id = response.data.country_id;
                    this.status_id = response.data.status_id;
                    this.object_crypt = response.data.object_crypt;
                    this.object_name = response.data.object_name;
                    this.stamp = response.data.stamp;
                    this.performer_own_company_id = response.data.performer_own_company_id;
                    this.customer_contractor_id = response.data.customer_contractor_id;
                    this.payer_contractor_id = response.data.payer_contractor_id;
                    this.payer_manager_individual_id = response.data.payer_manager_individual_id;
                    this.project_manager_individual_id = response.data.project_manager_individual_id;
                    this.archive = response.data.archive;
                    this.notice = response.data.notice;
                    this.finance_document_id = response.data.finance_document_id;
                    this.finance_document_content_id = response.data.finance_document_content_id;

                    this.getBaseFinanceDocuments();
                    this.getFinanceDocumentContent();
                    this.getProjectStatusesByCountry();
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
      getProjectStatusesByCountry: function(){
        this.projectStatusesManager.getAllByCountryId(this.country_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.statusItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getBaseFinanceDocuments: function(){
        this.$nextTick(()=>{
          this.financeDocumentsManager.getAllForInvoiceByContractor(this.payer_contractor_id)
                  .then( (response) => {
                    if(response.data !== false){
                      this.financeDocumentItems = response.data.items;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      },
      getFinanceDocumentContent: function(){
        this.financeDocumentsContentManager.findServicesByDocumentId(this.finance_document_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.financeDocumentContentItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      onChangePayer: function(){
        this.finance_document_id = 0;
        this.finance_document_content_id = 0;
        this.getBaseFinanceDocuments();
      },
      onChangeFinanceDocument: function(){
        this.finance_document_content_id = 0;
        this.getFinanceDocumentContent();
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
      getOwnCompanies: function () {
        this.ownCompaniesManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.ownCompaniesItems = response.data.items;
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
      getContractors: function () {
        this.contractorManager.getForProjectSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.contractor_Items = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      onCountryChange: function(){
        this.status_id = 0;
        this.finance_document_id = 0;
        this.finance_document_content_id = 0;
        this.financeDocumentItems = [];
        this.financeDocumentContentItems = [];
        this.getProjectStatusesByCountry();
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
          status_id: this.status_id,
          object_crypt: this.object_crypt,
          object_name: this.object_name,
          stamp: this.stamp,
          performer_own_company_id: this.performer_own_company_id,
          customer_contractor_id: this.customer_contractor_id,
          payer_contractor_id: this.payer_contractor_id,
          payer_manager_individual_id: this.payer_manager_individual_id,
          project_manager_individual_id: this.project_manager_individual_id,
          archive: this.archive,
          notice: this.notice,
          finance_document_id: this.finance_document_id,
          finance_document_content_id: this.finance_document_content_id
        };

        this.projectsManager.create(createData)
                .then( (response) => {
                    if (response.data !== false){
                      if (!response.data.error){
                        this.$eventHub.$emit(this.updateItemListNameTrigger);
                        // this.showDialog = false;
                        this.rowId = response.data
                      } else {
                          this.openErrorDialog(response.data.error);
                      }
                    }
                })
                .catch(function (error) {
                  console.log(error);
                });
        window.scrollToTop();
      },
      update: function(){

        var updateData = {
          name: this.name,
          country_id: this.country_id,
          status_id: this.status_id,
          object_crypt: this.object_crypt,
          object_name: this.object_name,
          stamp: this.stamp,
          performer_own_company_id: this.performer_own_company_id,
          customer_contractor_id: this.customer_contractor_id,
          payer_contractor_id: this.payer_contractor_id,
          payer_manager_individual_id: this.payer_manager_individual_id,
          project_manager_individual_id: this.project_manager_individual_id,
          archive: this.archive,
          notice: this.notice,
          finance_document_id: this.finance_document_id,
          finance_document_content_id: this.finance_document_content_id,
          id: this.rowId
        };

        this.projectsManager.update(updateData)
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
        this.name = '';
        this.country_id = null;
        this.status_id = 0;
        this.object_crypt = '';
        this.object_name = '';
        this.stamp = '';
        this.performer_own_company_id = null;
        this.customer_contractor_id = null;
        this.payer_contractor_id = null;
        this.payer_manager_individual_id = null;
        this.project_manager_individual_id = null;
        this.archive = '';
        this.notice = '';

        this.finance_document_id = 0;
        this.finance_document_content_id = 0;

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

    computed: {
      nameErrors () {
        const errors = []
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push(this.$store.state.t('Name must be at most 250 characters long'))
        !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      object_cryptErrors () {
        const errors = []
        if (!this.$v.object_crypt.$dirty) return errors
        !this.$v.object_crypt.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      object_nameErrors () {
        const errors = []
        if (!this.$v.object_name.$dirty) return errors
        !this.$v.object_name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      stampErrors () {
        const errors = []
        if (!this.$v.stamp.$dirty) return errors
        !this.$v.stamp.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      archiveErrors () {
        const errors = []
        if (!this.$v.archive.$dirty) return errors
        !this.$v.archive.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      country_idErrors () {
        const errors = []
        if (!this.$v.country_id.$dirty) return errors
        !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      status_idErrors () {
        const errors = []
        if (!this.$v.status_id.$dirty) return errors
        !this.$v.status_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      performer_own_company_idErrors () {
        const errors = []
        if (!this.$v.performer_own_company_id.$dirty) return errors
        !this.$v.performer_own_company_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      customer_contractor_idErrors () {
        const errors = []
        if (!this.$v.customer_contractor_id.$dirty) return errors
        !this.$v.customer_contractor_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      payer_contractor_idErrors () {
        const errors = []
        if (!this.$v.payer_contractor_id.$dirty) return errors
        !this.$v.payer_contractor_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>