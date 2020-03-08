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
                  <b-tab :title="$store.state.t('Entity Info')" active>
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
                            @change="onCountryChangeMainForm"
                    ></v-autocomplete>

                    <v-select
                            v-model="entity_type_id"
                            :items="entity_typesItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Entity Types')"
                            required
                    ></v-select>

                    <v-text-field
                            v-model="name"
                            :error-messages="nameErrors"
                            :counter="250"
                            :label="$store.state.t('Name')"
                            required
                            @input="$v.name.$touch()"
                            @blur="$v.name.$touch()"
                    ></v-text-field>
                    <v-text-field
                            v-model="short_name"
                            :error-messages="short_nameErrors"
                            :counter="250"
                            :label="$store.state.t('Short Name')"
                            required
                            @input="$v.short_name.$touch()"
                            @blur="$v.short_name.$touch()"
                    ></v-text-field>

                    <v-text-field
                            v-model="ogrn"
                            :counter="250"
                            :label="$store.state.t('OGRN')"
                    ></v-text-field>
                    <v-text-field
                            v-model="inn"
                            :counter="250"
                            :label="$store.state.t('INN')"
                    ></v-text-field>
                    <v-text-field
                            v-model="kpp"
                            :counter="250"
                            :label="$store.state.t('KPP')"
                    ></v-text-field>
                    <v-text-field
                            v-model="okpo"
                            :counter="250"
                            :label="$store.state.t('OKPO')"
                    ></v-text-field>

                    <v-textarea
                            v-model="notice"
                            :label="$store.state.t('Notice')"
                    ></v-textarea>

                    <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                    <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
                  </b-tab>
                  <b-tab :title="$store.state.t('Personal')">
                    <div v-if="parseInt(rowId) > 0">
                      <personal_tab_list_component
                              v-if="parseInt(rowId) > 0"
                              :showCardTitle=false
                              :contractorIsEntity=parseInt(1)
                              :contractorRefId=parseInt(rowId)
                              :notOriginalPage=true
                      >
                      </personal_tab_list_component>

                      <br />
                      <v-btn  @click="cancel">{{$store.state.t('To List')}}</v-btn>
                    </div>
                    <div v-else>
                      <b-button class="mr-2 mb-2 btn-pill" variant="success" @click="addToPull('personal')">{{$store.state.t('Add Personal')}}</b-button>
                      <br />

                      <div style="margin-top: 10px" v-for="personal in pullPersonals">
                        <v-autocomplete
                                v-model="personal.individual_id"
                                :items="individualsItems"
                                item-value="id"
                                item-text="nameWithId"
                                :label="$store.state.t('Personal')"
                        ></v-autocomplete>
                        <v-text-field
                                v-model="personal.position"
                                :counter="250"
                                :label="$store.state.t('Position')"
                        ></v-text-field>
                        <v-textarea
                                v-model="personal.notice"
                                :label="$store.state.t('Notice')"
                        ></v-textarea>
                      </div>


                      <br />
                      <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                      <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
                    </div>

                  </b-tab>
                  <b-tab :title="$store.state.t('Contacts')">
                    <div v-if="parseInt(rowId) > 0">
                      <contacts-list
                              v-if="parseInt(rowId) > 0"
                              :showCardTitle=false
                              :contractorIsEntity=parseInt(1)
                              :contractorRefId=parseInt(rowId)
                              :expectedFields=contactsExpectedFields
                              :notOriginalPage=true
                      >
                      </contacts-list>
                      <br />
                      <v-btn  @click="cancel">{{$store.state.t('To List')}}</v-btn>
                    </div>
                    <div v-else>
                      <b-button class="mr-2 mb-2 btn-pill" variant="success" @click="addToPull('contact')">{{$store.state.t('Add Contact')}}</b-button>
                      <br />

                      <div style="margin-top: 10px" v-for="(contact, contactIndex) in pullContacts">
                        <v-select
                                v-model="contact.contact_type_id"
                                :items="contact_typesItems"
                                item-value="id"
                                item-text="contact_type"
                                :label="$store.state.t('Contact Type')"
                                @change="selectContactInput(contact)"
                        ></v-select>

                        <v-text-field
                                v-if="contact.settledContactInputType === '' || contact.settledContactInputType === null || contact.settledContactInputType === 0"
                                v-model="contact.contact_name"
                                :error-messages="nameErrors"
                                :counter="250"
                                :label="$store.state.t('Contact')"
                                required
                                @input="$v.name.$touch()"
                                @blur="$v.name.$touch()"
                                :disabled="contact.contact_type_id <= 0"
                        ></v-text-field>
                        <v-autocomplete
                                v-if="contact.settledContactInputType === 'phone'"
                                v-model="contact.phoneCountryId"
                                :items="phoneCountriesList"
                                item-value="id"
                                item-text="name"
                                :label="$store.state.t('put International Phone Code or Country')"
                                @change="onChangePhoneCountry(contact, contactIndex)"
                        ></v-autocomplete>
                        <v-text-field
                                v-if="contact.settledContactInputType === 'phone' && contact.phoneCountryId > 0"
                                v-model="contact.contact_name"
                                :error-messages="nameErrors"
                                :label="$store.state.t('Phone')"
                                return-masked-value
                                :mask="contact.phoneMask"
                                autofocus
                        ></v-text-field>
                        <v-text-field
                                v-if="contact.settledContactInputType === 'email'"
                                v-model="contact.contact_name"
                                :label="$store.state.t('e-mail')"
                                :rules="[rules.email]"
                        ></v-text-field>

                        <v-textarea
                                v-model="contact.contact_notice"
                                :label="$store.state.t('Notice')"
                        ></v-textarea>
                      </div>


                      <br />
                      <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                      <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
                    </div>

                  </b-tab>
                  <b-tab :title="$store.state.t('Addresses')">
                    <div v-if="parseInt(rowId) > 0">
                      <addresses-list
                              v-if="parseInt(rowId) > 0"
                              :showCardTitle=false
                              :contractorIsEntity=parseInt(1)
                              :contractorRefId=parseInt(rowId)
                              :expectedFields=addressesExpectedFields
                              :exceptedFields=addressesExceptedFields
                              :notOriginalPage=true
                      >
                      </addresses-list>
                      <br />
                      <v-btn  @click="cancel">{{$store.state.t('To List')}}</v-btn>
                    </div>
                    <div v-else>
                      <b-button class="mr-2 mb-2 btn-pill" variant="success" @click="addToPull('address')">{{$store.state.t('Add Address')}}</b-button>
                      <br />

                      <b-tabs card>
                        <b-tab :title="$store.state.t('Address')" v-for="address in pullAddresses">
                          <v-select
                                  v-model="address.address_type_id"
                                  :items="address_typesItems"
                                  item-value="id"
                                  item-text="address_type"
                                  :label="$store.state.t('Address Type')"
                          ></v-select>
                          <v-autocomplete
                                  v-model="address.country_id_for_contacts"
                                  :items="countryItems"
                                  item-value="id"
                                  item-text="name"
                                  :label="$store.state.t('Country')"
                                  @change="onCountryChange(address)"
                          ></v-autocomplete>
                          <v-autocomplete
                                  v-model="address.region_id_for_contacts"
                                  :items="address.regionItems"
                                  item-value="id"
                                  item-text="name"
                                  :label="$store.state.t('Region')"
                                  @change="onRegionChange(address)"
                          ></v-autocomplete>
                          <v-autocomplete
                                  v-model="address.city_id"
                                  :items="address.citiesItems"
                                  item-value="id"
                                  item-text="name"
                                  :label="$store.state.t('City')"
                          ></v-autocomplete>
                          <v-text-field
                                  v-model="address.index"
                                  :label="$store.state.t('Index')"
                          ></v-text-field>
                          <v-textarea
                                  v-model="address.address"
                                  :label="$store.state.t('Address')"
                          ></v-textarea>
                          <v-textarea
                                  v-model="address.notice"
                                  :label="$store.state.t('Notice')"
                          ></v-textarea>
                        </b-tab>

                        <br />
                        <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                        <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>

                      </b-tabs>
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
    <confirmator
            :handlerInputProcessName.sync="confirmatorInputProcessName"
            :handlerOutputProcessName.sync="confirmatorOutputProcessName">
    </confirmator>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';
  import flag from "../../components/flag";
  import addressesList from "../addresses/table_list_component";
  import contactsList from "../contacts/table_list_component";
  import personal_tab_list_component from "./personal_tab_list_component";
  import loadercustom from "../../components/loadercustom";
  import confirmator from "../../components/confirmator";

  import {CountriesManager} from '../../../managers/CountriesManager'
  import {EM} from "../../../managers/EntitiesManager";
  import {AddressTypesManager} from "../../../managers/AddressTypesManager";
  import {CitiesManager} from "../../../managers/CitiesManager";
  import {PM} from "../../../managers/PersonalManager";
  import {IM} from "../../../managers/IndividualsManager";
  import {RegionsManager} from "../../../managers/RegionsManager";
  import {ContactTypesManager} from "../../../managers/ContactTypesManager";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";
  import customValidationMixin from "../../../mixins/customValidationMixin";


  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      flag,
      CountriesManager,
      addressesList,
      contactsList,
      personal_tab_list_component,
      EM,
      AddressTypesManager,
      CitiesManager,
      loadercustom,
      confirmator,
    },

    mixins: [validationMixin, customValidationMixin],

    validations: {
      name: { required, maxLength: maxLength(250) },
      short_name: { required, maxLength: maxLength(250) },
      country_id: { required },
    },

    data () {
      return {
        customDialogfrontString: 'Please stand by',
        showCustomLoaderDialog: false,
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        name: '',
        short_name: '',
        ogrn: '',
        inn: '',
        kpp: '',
        okpo: '',
        notice: '',
        entity_type_id: null,
        entity_typesItems: [],
        country_id: null,
        countryItems: [],

        contact_typesItems: [],
        address_typesItems: [],

        individualsItems: [],
        duplicateEntitiesInCreating: false,
        confirmatorInputProcessName: 'confirm:forcePersonal',
        confirmatorOutputProcessName: 'confirmed:forcePersonal',
        forceSaveUpdate: false,
        tabIndex: 0,


        contactsExpectedFields: ['actions', 'contact_type', 'name', 'notice'],
        addressesExpectedFields: ['actions', 'address_type', 'country_name', 'region_name', 'city', 'address'],
        addressesExceptedFields: [],

        pullContacts: [
          {
            contact_name: '',
            contact_notice: '',
            contact_type_id: null,
            settledContactInputType: '',
            emailIsValid: true,
            phoneCountryId: null
          }
        ],
        pullPersonals: [
          {
            individual_id: null,
            position: '',
            notice: ''
          }
        ],
        pullAddresses: [
          {
            regionItems: [],
            citiesItems: [],
            country_id_for_contacts: null,
            region_id_for_contacts: null,
            address_type_id: null,
            city_id: null,
            index: '',
            address: '',
            notice: ''
          }
        ],

        phoneCountriesList: [],

        inputTypes: {},
        rules: {
          email: value => {
            if (value === '' || value === null){
              return true;
            }
            return this.emailValidation(value) || 'Invalid e-mail.'
          }
        }
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
      showListEventName: {type: String, require: false},
    },
    created() {
      this.contactTypesManager = new ContactTypesManager();
      this.countriesManager = new CountriesManager();
      this.entitiesManager = new EM();
      this.personalManager = new PM();
      this.individualsManager = new IM();
      this.addressTypesManager = new AddressTypesManager();
      this.citiesManager = new CitiesManager();
      this.regionsManager = new RegionsManager();

      this.getCountriesForSelect();
      this.getContactTypes();
      this.getContactInputTypes();
      this.getAllPhoneCodeList();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.getAddressTypes();

        this.getIndividuals();
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.rowId = data.id;
        axios.get(window.apiDomainUrl+'/entities/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.short_name = response.data.short_name;
                    this.notice = response.data.notice;
                    this.entity_type_id = response.data.entity_type_id;
                    this.country_id = response.data.country_id;
                    this.ogrn = response.data.ogrn;
                    this.inn = response.data.inn;
                    this.kpp = response.data.kpp;
                    this.okpo = response.data.okpo;
                    this.getEntityTypesByCountryId();
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.header = this.$store.state.t('Updating')+'...';
        this.showDialog = true;
      });

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.forceSaveUpdate = true;
        this.create();
      });
    },

    methods: {
      addToPull(type){

        switch (type) {
          case 'contact':

            this.pullContacts.unshift(
                    {
                      contact_name: '',
                      contact_notice: '',
                      contact_type_id: null,
                      settledContactInputType: '',
                      emailIsValid: true,
                      phoneCountryId: null,
                      phoneMask: ''
                    }
            );
            break;

          case 'personal':

            this.pullPersonals.unshift(
                    {
                      individual_id: null,
                      position: '',
                      notice: ''
                    }
            );
            break;

          case 'address':

            this.pullAddresses.unshift(
                    {
                      regionItems: [],
                      citiesItems: [],
                      country_id_for_contacts: null,
                      region_id_for_contacts: null,
                      address_type_id: null,
                      city_id: null,
                      index: '',
                      address: '',
                      notice: ''
                    }
            );
            break;

        }

      },
      selectContactInput(contact){
        var inputType = this.contact_typesItems.find(type => type.id === contact.contact_type_id).input_type;
        if (typeof inputType !== 'undefined' && inputType !== null && typeof this.inputTypes[inputType] !== 'undefined'){
          contact.settledContactInputType = this.inputTypes[inputType];
        } else {
          contact.settledContactInputType = ''
        }
        contact.contact_name = null;
        contact.emailIsValid = true;
        contact.phoneCountryId = null;
        contact.phoneMask = '';
      },
      getAllPhoneCodeList: function () {
        this.countriesManager.getAllPhoneCodeList()
                .then( (response) => {
                  if(response.data !== false){
                    this.phoneCountriesList = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getContactInputTypes: function () {
        this.contactTypesManager.getContactInputTypes()
                .then( (response) => {
                  if(response.data !== false){
                    this.inputTypes = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      onChangePhoneCountry: function(contact, contactIndex){
        var countryPhoneObject = this.phoneCountriesList.find(country => country.id === contact.phoneCountryId);
        if (countryPhoneObject.id > 0){
          contact.phoneMask = countryPhoneObject.phone_mask;
          contact.contact_name = '+'+countryPhoneObject.phone_code;
        }
      },
      prepareContacts() {
        var firstFiltered = this.pullContacts.filter(function (item) {
          return (item.contact_name.trim() !== '' &&  item.contact_type_id !== null);
        });

        var filtered = firstFiltered.filter((item) => {
          if (item.settledContactInputType === 'email' && !this.emailValidation(item.contact_name)){
            return false;
          } else {
            return true;
          }
        });

        var tmpObject = {};
        filtered.forEach(function(item){
          tmpObject[Math.random().toFixed(5)] = item;
        });

        var resultArray = [];
        for (let [key, value] of Object.entries(tmpObject)) {
          resultArray.push(value);
        }

        return resultArray;
      },
      preparePersonal() {
        this.duplicateEntitiesInCreating = false;
        var filtered = this.pullPersonals.filter(function (item) {
          return (item.position.trim() !== '' &&  item.individual_id !== null);
        });

        var tmpObject = {};
        filtered.forEach((item) => {
          if (typeof tmpObject[item.individual_id] !== 'undefined'){
            this.duplicateEntitiesInCreating = true;
          }
          tmpObject[item.individual_id] = item;
        });

        var resultArray = [];
        for (let [key, value] of Object.entries(tmpObject)) {
          resultArray.push(value);
        }

        return resultArray;
      },
      prepareAddresses() {
        var filtered = this.pullAddresses.filter(function (item) {
          return (item.address.trim() !== '' &&  item.address_type_id !== null &&  item.city_id !== null &&  item.country_id_for_contacts !== null &&  item.region_id_for_contacts !== null);
        });

        var tmpObject = {};
        filtered.forEach(function(item){
          tmpObject[Math.random().toFixed(5)] = item;
        });

        var resultArray = [];
        for (let [key, value] of Object.entries(tmpObject)) {
          delete(value.regionItems);
          delete(value.citiesItems);
          resultArray.push(value);
        }

        return resultArray;
      },
      getCountriesForSelect: function () {
        this.countriesManager.getForSelectAccordingRegions()
                .then( (response) => {
                  if(response.data !== false){
                    this.countryItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getEntityTypesByCountryId: function () {
        this.entitiesManager.getEntityTypesByCountryId(this.country_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.entity_typesItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      onCountryChangeMainForm: function(){
        this.getEntityTypesByCountryId();
      },
      onCountryChange: function(address){
        address.region_id_for_contacts = null;
        this.selectRegionByCountry(address);
        this.onRegionChange(address);
      },
      selectRegionByCountry: function(address){
        this.regionsManager.getForSelectByCountry(address.country_id_for_contacts)
                .then( (response) => {
                  if(response.data !== false){
                    address.regionItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      onRegionChange: function(address){
        address.city_id = null;
        this.getCitiesByRegion(address);
      },
      getCitiesByRegion: function(address){
        this.citiesManager.getForSelectByRegion(address.region_id_for_contacts)
                .then( (response) => {
                  if(response.data !== false){
                    address.citiesItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getAddressTypes: function () {
        this.addressTypesManager.getForSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.address_typesItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getContactTypes: function () {
        axios.get(window.apiDomainUrl+'/contact-types/get-all-for-select', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.contact_typesItems = response.data.items;
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
                    this.individualsItems = response.data.items;
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
        } else {
          this.tabIndex = 0;
        }
      },
      create: function(){

        var createData = {
          name: this.name,
          short_name: this.short_name,
          notice: this.notice,
          country_id: this.country_id,
          entity_type_id: this.entity_type_id,
          ogrn: this.ogrn,
          inn: this.inn,
          kpp: this.kpp,
          okpo: this.okpo,

          pullContacts: this.prepareContacts(),
          pullPersonals: this.preparePersonal(),
          pullAddresses: this.prepareAddresses(),
          force_action: this.forceSaveUpdate,
        };

        if (this.duplicateEntitiesInCreating === true){
          this.openErrorDialog('You set some duplicate values. Individual can not have several positions in one entity.');
          return true;
        }

        axios.post(window.apiDomainUrl+'/entities/create', qs.stringify(createData))
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
                      this.$eventHub.$emit(this.showListEventName);
                      this.setDefaultData();
                    } else {
                      if (response.data.duplicate){
                        this.$eventHub.$emit(this.confirmatorInputProcessName, {
                          titleString: this.$store.state.t('Force Creating Personal') + '...',
                          confirmString: this.$store.state.t(response.data.error)
                        });
                      } else {
                        this.openErrorDialog(response.data.error);
                        this.forceSaveUpdate = false;
                      }
                    }
                  }
                })
                .catch((error) => {
                  this.setDefaultData();
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          name: this.name,
          short_name: this.short_name,
          notice: this.notice,
          country_id: this.country_id,
          entity_type_id: this.entity_type_id,
          ogrn: this.ogrn,
          inn: this.inn,
          kpp: this.kpp,
          okpo: this.okpo,

          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/entities/update', qs.stringify(updateData))
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
                      this.$eventHub.$emit(this.showListEventName);
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
        this.$eventHub.$emit(this.showListEventName);
      },

      setDefaultData () {
        this.name = '';
        this.short_name = '';
        this.ogrn = '';
        this.inn = '';
        this.kpp = '';
        this.okpo = '';
        this.notice = '';
        this.entity_type_id = null;
        this.country_id = null;
        this.forceSaveUpdate = false;

        this.rowId = 0;

        this.pullContacts = [
          {
            contact_name: '',
            contact_notice: '',
            contact_type_id: null,
            settledContactInputType: '',
            emailIsValid: true,
            phoneCountryId: null,
            phoneMask: ''
          }
        ];
        this.pullAddresses = [
          {
            regionItems: [],
            citiesItems: [],
            country_id_for_contacts: null,
            region_id_for_contacts: null,
            address_type_id: null,
            city_id: null,
            index: '',
            address: '',
            notice: ''
          }
        ];
        this.pullPersonals = [
          {
            individual_id: null,
            position: '',
            notice: ''
          }
        ];
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
        !this.$v.name.maxLength && errors.push(this.$store.state.t('Name must be at most 250 characters long'))
        !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      short_nameErrors () {
        const errors = [];
        if (!this.$v.short_name.$dirty) return errors;
        !this.$v.short_name.maxLength && errors.push(this.$store.state.t('Short Name must be at most 250 characters long'))
        !this.$v.short_name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      country_idErrors () {
        const errors = [];
        if (!this.$v.country_id.$dirty) return errors;
        !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
      this.$eventHub.$off(this.confirmatorOutputProcessName);
    },
  }
</script>
