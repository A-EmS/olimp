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
                    <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
                    <div v-if="errorMessage !== null" class="alert alert-danger">
                      {{errorMessage}}
                    </div>
                    <v-text-field
                        v-model="code"
                        :counter="255"
                        :label="$store.state.t('Code')"
                    ></v-text-field>
                    <v-text-field
                        v-model="notice"
                        :label="$store.state.t('Notice')"
                        :counter="250"
                    ></v-text-field>

                    <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                    <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
            </b-col>
          </b-row>
        </v-form>
      </demo-card>

    </layout-wrapper>

    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
import DemoCard from '../../../Layout/Components/DemoCard';

import { validationMixin } from 'vuelidate'
import {required} from 'vuelidate/lib/validators'
import loadercustom from "../../components/loadercustom";
import {OwnCompaniesManager} from "@/managers/OwnCompaniesManager";

import {DocumentTypesManager} from "@/managers/DocumentTypesManager";
import {CountriesManager} from "@/managers/CountriesManager";
import {PatternsManager} from "@/managers/PatternsManager";

export default {
  components: {
    'layout-wrapper': LayoutWrapper,
    'demo-card': DemoCard,
    loadercustom
  },

  mixins: [validationMixin],

  validations: {
    file: { required },
    name: { required },
    country_id: { required },
    document_type_id: { required },
    own_company_id: { required },
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
      acceptableExtensions: ['docx',],
      errorMessage: null,


      name: null,
      code: '',
      country_id: null,
      file: null,
      document_type_id: null,
      own_company_id: null,
      notice: '',

      countryItems: [],
      documentTypeItems: [],
      ownCompanyItems: [],
    }
  },
  props: {
    createProcessNameTrigger: {type: String, require: false},
    updateProcessNameTrigger: {type: String, require: false},
    updateItemListNameTrigger: {type: String, require: false},
  },
  created() {
    this.countriesManager = new CountriesManager();
    this.documentTypeManager = new DocumentTypesManager();
    this.ownCompaniesManager = new OwnCompaniesManager();
    this.patternsManager = new PatternsManager();

    this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
      this.header = this.$store.state.t('Creating new')+'...';
      this.initFormComponent();
      this.setDefaultData();
      this.showDialog = true;
    });

    this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
      this.initFormComponent();
      this.rowId = 0;
      this.patternsManager.getById(data.id)
          .then( (response) => {
            if(response.data !== false){
              this.rowId = response.data.id;
              this.name = response.data.name;
              this.code = response.data.code;
              this.country_id = response.data.country_id;
              this.document_type_id = response.data.document_type_id;
              this.own_company_id = response.data.own_company_id;
              this.notice = response.data.notice;

              this.header = this.$store.state.t('Updating')+'...'+this.code;
            }
          }).then(() => {
              this.selectDocumentTypeByCountry();
          })
          .catch(function (error) {
            console.log(error);
          });
      this.showDialog = true;
    });

  },

  methods: {
    initFormComponent: function(){
      // this.getDocumentTypes();
      this.getOwnCompanies();
      this.getCountriesForSelect();
    },
    handleFileUpload(){
      this.file = this.$refs.file.files[0];
      this.errorMessage = null;
      if (!this.isAcceptableFile(this.file)) {
        this.errorMessage = this.$store.state.t('Just only .docx');
        this.file = null;
        return;
      }
    },
    isAcceptableFile(file) {
      let emptyFile = (file === null || typeof file === 'undefined');
      var extension = file.name.substr(file.name.lastIndexOf('.') + 1);
      return !emptyFile && this.acceptableExtensions.includes(extension);
    },
    onCountryChange: function(){
      this.selectDocumentTypeByCountry();
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
    // getDocumentTypes: function () {
    //   this.documentTypeManager.getAll()
    //       .then( (response) => {
    //         if(response.data !== false){
    //           this.documentTypeItems = response.data.items;
    //         }
    //       })
    //       .catch(function (error) {
    //         console.log(error);
    //       });
    // },
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

      let formData = new FormData();
      formData.append('file', this.file);
      formData.append('name', this.name);
      formData.append('code', this.code);
      formData.append('country_id', this.country_id);
      formData.append('document_type_id', this.document_type_id);
      formData.append('own_company_id', this.own_company_id);
      formData.append('notice', this.notice);

      this.patternsManager.create(formData)
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
      let formData = new FormData();
      formData.append('file', this.file);
      formData.append('name', this.name);
      formData.append('code', this.code);
      formData.append('country_id', this.country_id);
      formData.append('document_type_id', this.document_type_id);
      formData.append('own_company_id', this.own_company_id);
      formData.append('notice', this.notice);
      formData.append('id', this.rowId);

      this.patternsManager.update(formData)
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
      this.name = null;
      this.code = '';
      this.country_id = null;
      this.document_type_id = null;
      this.own_company_id = null;
      this.notice = '';
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
    country_idErrors () {
      const errors = []
      if (!this.$v.country_id.$dirty) return errors
      !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
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
    nameErrors () {
      const errors = []
      if (!this.$v.name.$dirty) return errors
      !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
      return errors
    },
  },

  beforeDestroy () {
    this.$eventHub.$off(this.createProcessNameTrigger);
    this.$eventHub.$off(this.updateProcessNameTrigger);
  },
}
</script>
