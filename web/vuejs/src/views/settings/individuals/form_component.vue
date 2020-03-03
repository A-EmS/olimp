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
                    <b-tab :title="$store.state.t('User Info')" active>
                          <v-text-field
                                  v-model="third_name"
                                  :error-messages="third_nameErrors"
                                  :counter="255"
                                  :label="$store.state.t('Third Name')"
                                  required
                                  @input="$v.third_name.$touch()"
                                  @blur="$v.third_name.$touch()"
                          ></v-text-field>
                          <v-text-field
                                  v-model="name"
                                  :error-messages="nameErrors"
                                  :counter="255"
                                  :label="$store.state.t('Name')"
                                  required
                                  @input="$v.name.$touch()"
                                  @blur="$v.name.$touch()"
                          ></v-text-field>
                          <v-text-field
                                  v-model="second_name"
                                  :counter="255"
                                  :label="$store.state.t('Second Name')"
                          ></v-text-field>
                          <v-select
                                  v-model="gender"
                                  :error-messages="genderErrors"
                                  :items="[{id:'0', name:$store.state.t('Female')}, {id:'1', name:$store.state.t('Male')}]"
                                  item-value="id"
                                  item-text="name"
                                  :label="$store.state.t('Gender')"
                                  required
                                  @input="$v.gender.$touch()"
                                  @blur="$v.gender.$touch()"
                          ></v-select>

                                <v-menu
                                        ref="birthdayMenu"
                                        v-model="birthdayMenu"
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
                                                v-model="birthday"
                                                :label="$store.state.t('Birthday')"
                                                prepend-icon="event"
                                                readonly
                                                v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                            ref="pickerBirthday"
                                            v-model="birthday"
                                            :locale="$store.state.user.settings.interface_language"
                                            :max="new Date().toISOString().substr(0, 10)"
                                            min="1950-01-01"
                                            @change="changeBirthday"
                                    ></v-date-picker>
                                </v-menu>

                          <v-text-field
                                  v-model="inn"
                                  :counter="255"
                                  :label="$store.state.t('INN')"
                          ></v-text-field>

                        <br />
                        <br />
                        <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                        <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
                    </b-tab>

                    <b-tab :title="$store.state.t('Passport Data')">
                          <v-text-field
                                  v-model="passport_series"
                                  :counter="255"
                                  :label="$store.state.t('Passport Series')"
                          ></v-text-field>

                          <v-text-field
                                  v-model="passport_number"
                                  :counter="255"
                                  :label="$store.state.t('Passport Number')"
                          ></v-text-field>

                          <v-text-field
                                  v-model="passport_authority"
                                  :counter="255"
                                  :label="$store.state.t('Passport Authority')"
                          ></v-text-field>

                        <v-menu
                                ref="authorityDateMenu"
                                v-model="authorityDateMenu"
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
                                        v-model="passport_authority_date"
                                        :label="$store.state.t('Passport Authority Date')"
                                        prepend-icon="event"
                                        readonly
                                        v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                    ref="pickerAuthorityDate"
                                    v-model="passport_authority_date"
                                    :locale="$store.state.user.settings.interface_language"
                                    :max="new Date().toISOString().substr(0, 10)"
                                    min="1950-01-01"
                                    @change="changeAuthorityDate"
                            ></v-date-picker>
                        </v-menu>

                          <v-text-field
                                  v-model="notice"
                                  :label="$store.state.t('Notice')"
                          ></v-text-field>

                        <br />
                        <br />
                        <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                        <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
                    </b-tab>
                    <b-tab :title="$store.state.t('Contacts')">
                        <div v-if="parseInt(rowId) > 0">
                          <contacts-list
                              v-if="parseInt(rowId) > 0"
                              :showCardTitle=false
                              :contractorIsEntity=parseInt(0)
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
                                        :label="$store.state.t('Entity Type')"
                                        @change="selectContactInput(contact)"
                                ></v-select>
                                    <v-text-field
                                            v-if="contact.settledContactInputType === '' || contact.settledContactInputType === null"
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
                                  :contractorIsEntity=parseInt(0)
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
                  <b-tab :title="$store.state.t('Entities')">
                      <div v-if="parseInt(rowId) > 0">
                          <entity_tab_list_component
                                  v-if="parseInt(rowId) > 0"
                                  :showCardTitle=false
                                  :contractorIsEntity=parseInt(0)
                                  :contractorRefId=parseInt(rowId)
                                  :notOriginalPage=true
                          >
                          </entity_tab_list_component>

                          <br />
                          <v-btn  @click="cancel">{{$store.state.t('To List')}}</v-btn>
                      </div>
                      <div v-else>
                          <b-button class="mr-2 mb-2 btn-pill" variant="success" @click="addToPull('entity')">{{$store.state.t('Add Entity')}}</b-button>
                          <br />

                          <div style="margin-top: 10px" v-for="entity in pullEntities">
                              <v-autocomplete
                                      v-model="entity.entity_id"
                                      :items="entitiesItems"
                                      item-value="id"
                                      item-text="name"
                                      :label="$store.state.t('Entity')"
                              ></v-autocomplete>
                              <v-text-field
                                      v-model="entity.position"
                                      :counter="250"
                                      :label="$store.state.t('Position')"
                              ></v-text-field>
                              <v-textarea
                                      v-model="entity.notice"
                                      :label="$store.state.t('Notice')"
                              ></v-textarea>
                          </div>


                          <br />
                          <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                          <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
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
  import contactsList from "../contacts/table_list_component";
  import addressesList from "../addresses/table_list_component";
  import entity_tab_list_component from "./entity_tab_list_component";
  import loadercustom from "../../components/loadercustom";
  import confirmator from "../../components/confirmator";
  import {EM} from "../../../managers/EntitiesManager";
  import {AddressTypesManager} from "../../../managers/AddressTypesManager";
  import {CitiesManager} from "../../../managers/CitiesManager";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";
  import {CountriesManager} from "../../../managers/CountriesManager";
  import {RegionsManager} from "../../../managers/RegionsManager";
  import customValidationMixin from "../../../mixins/customValidationMixin";
  import {ContactTypesManager} from "../../../managers/ContactTypesManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      'contactsList': contactsList,
        addressesList,
        entity_tab_list_component,
        loadercustom,
        confirmator,
        EM,
        AddressTypesManager,
        CitiesManager
    },

    mixins: [validationMixin, customValidationMixin],

    validations: {
      name: { required, maxLength: maxLength(255) },
      third_name: { required, maxLength: maxLength(255) },
      gender: { required, maxLength: maxLength(255) },
    },
    watch: {
        birthdayMenu (val) {
            val && setTimeout(() => (this.$refs.pickerBirthday.activePicker = 'YEAR'))
        },
        authorityDateMenu (val) {
            val && setTimeout(() => (this.$refs.pickerAuthorityDate.activePicker = 'YEAR'))
        }
    },
    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',
        showDialog: false,

        confirmatorInputProcessName: 'confirm:forceIndividual',
        confirmatorOutputProcessName: 'confirmed:forceIndividual',
        forceSaveUpdate: false,
        duplicateEntitiesInCreating: false,

        tabIndex: 0,

        valid: true,
        header: 'Action...',
        rowId: 0,
        name: '',
        second_name: '',
        third_name: '',
        gender: '',
        birthday: '',
        inn: '',
        passport_series: '',
        passport_number: '',
        passport_authority: '',
        passport_authority_date: '',
        notice: '',

        countryItems: [],
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
        pullEntities: [
          {
              entity_id: null,
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
        contact_typesItems: [],
        entitiesItems: [],
        address_typesItems: [],
        cities_Items: [],

        contactsExpectedFields: ['actions', 'contact_type', 'name', 'notice'],
        addressesExpectedFields: ['actions', 'address_type', 'country_name', 'region_name', 'city', 'address'],
        addressesExceptedFields: [],

        birthdayMenu: false,
        authorityDateMenu: false,


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
        this.entitiesManager = new EM();
        this.addressTypesManager = new AddressTypesManager();
        this.citiesManager = new CitiesManager();
        this.countriesManager = new CountriesManager();
        this.citiesManager = new CitiesManager();
        this.regionsManager = new RegionsManager();

        this.getContactTypes();
        this.getContactInputTypes();
        this.getAllPhoneCodeList();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.getEntities();
        this.getAddressTypes();
        this.getCountriesForSelect();
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
          this.rowId = data.id;
        axios.get(window.apiDomainUrl+'/individuals/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.second_name = response.data.second_name;
                    this.third_name = response.data.third_name;
                    this.gender = response.data.gender;
                    this.birthday = response.data.birthday;
                    this.inn = response.data.inn;
                    this.passport_series = response.data.passport_series;
                    this.passport_number = response.data.passport_number;
                    this.passport_authority = response.data.passport_authority;
                    this.passport_authority_date = response.data.passport_authority_date;
                    this.notice = response.data.notice;
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

                case 'entity':

                    this.pullEntities.unshift(
                        {
                            entity_id: null,
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
            if (typeof inputType !== 'undefined' && inputType !== null){
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
        prepareEntities() {
            this.duplicateEntitiesInCreating = false;
            var filtered = this.pullEntities.filter(function (item) {
                return (item.position.trim() !== '' &&  item.entity_id !== null);
            });

            var tmpObject = {};
            filtered.forEach((item) => {
                if (typeof tmpObject[item.entity_id] !== 'undefined'){
                    this.duplicateEntitiesInCreating = true;
                }
                tmpObject[item.entity_id] = item;
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
        getEntities: function () {
            this.entitiesManager.getForSelect()
                .then( (response) => {
                    if(response.data !== false){
                        this.entitiesItems = response.data.items;
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
        getCities: function(){
            this.citiesManager.getForSelect()
                .then( (response) => {
                    if(response.data !== false){
                        this.cities_Items = response.data.items;
                        this.cities_Items.unshift({'id':0, 'name':'<none>'})
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        changeBirthday (date) {
            this.$refs.birthdayMenu.save(date)
        },
        changeAuthorityDate (date) {
            this.$refs.authorityDateMenu.save(date)
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
            second_name: this.second_name,
            third_name: this.third_name,
            gender: this.gender,
            birthday: this.birthday,
            inn: this.inn,
            passport_series: this.passport_series,
            passport_number: this.passport_number,
            passport_authority: this.passport_authority,
            passport_authority_date: this.passport_authority_date,
            notice: this.notice,

            pullContacts: this.prepareContacts(),
            pullEntities: this.prepareEntities(),
            pullAddresses: this.prepareAddresses(),
            force_action: this.forceSaveUpdate
        };

        if (this.duplicateEntitiesInCreating === true){
            this.openErrorDialog('You set some duplicate values. Individual can not have several positions in one entity.');
            return true;
        }

        axios.post(window.apiDomainUrl+'/individuals/create', qs.stringify(createData))
                .then( (response) => {
                  if (response.data !== false){
                      if (!response.data.error){
                          this.$eventHub.$emit(this.updateItemListNameTrigger);
                          this.cancel();
                          this.setDefaultData();
                      } else {
                          if (response.data.duplicate){
                              this.$eventHub.$emit(this.confirmatorInputProcessName, {
                                  titleString: this.$store.state.t('Force Creating Individual') + '...',
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
          second_name: this.second_name,
          third_name: this.third_name,
          gender: this.gender,
          birthday: this.birthday,
          inn: this.inn,
          passport_series: this.passport_series,
          passport_number: this.passport_number,
          passport_authority: this.passport_authority,
          passport_authority_date: this.passport_authority_date,
          notice: this.notice,
          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/individuals/update', qs.stringify(updateData))
                .then( (response) => {
                  if (response.data !== false){
                      if (!response.data.error){
                          this.$eventHub.$emit(this.updateItemListNameTrigger);
                          this.cancel();
                          this.setDefaultData();
                      } else {
                          this.openErrorDialog(response.data.error);
                      }
                  }
                })
                .catch((error) => {
                    console.log(error);
                    this.setDefaultData();
                });
      },
      cancel () {
        this.$v.$reset();
        this.showDialog = false;
        this.$eventHub.$emit(this.showListEventName);
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

      setDefaultData () {
        this.name = '';
        this.second_name = '';
        this.third_name = '';
        this.gender = '';
        this.birthday = '';
        this.inn = '';
        this.passport_series = '';
        this.passport_number = '';
        this.passport_authority = '';
        this.passport_authority_date = '';
        this.notice = '';
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
        this.pullEntities = [
            {
                entity_id: null,
                position: '',
                notice: ''
            }
        ];
      }
    },

    computed: {
      nameErrors () {
        const errors = []
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push(this.$store.state.t('Name must be at most 255 characters long'))
        !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      third_nameErrors () {
        const errors = []
        if (!this.$v.third_name.$dirty) return errors
        !this.$v.third_name.maxLength && errors.push(this.$store.state.t('Third Name must be at most 255 characters long'))
        !this.$v.third_name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      genderErrors () {
        const errors = []
        if (!this.$v.gender.$dirty) return errors
        !this.$v.gender.required && errors.push(this.$store.state.t('Required field'))
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
