<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >
          <v-select
                  v-model="address_type_id"
                  :error-messages="address_type_idErrors"
                  :items="address_typesItems"
                  item-value="id"
                  item-text="address_type"
                  :label="$store.state.t('Address Type')"
                  required
                  @input="$v.address_type_id.$touch()"
                  @blur="$v.address_type_id.$touch()"
          ></v-select>
          <v-select
                  v-model="contractor_id"
                  :error-messages="contractor_idErrors"
                  :items="contractor_Items"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Contractor')"
                  required
                  @input="$v.contractor_id.$touch()"
                  @blur="$v.contractor_id.$touch()"
          ></v-select>
          <v-select
                  v-model="city_id"
                  :items="cities_Items"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('City')"
          ></v-select>

          <v-text-field
                  v-model="index"
                  :label="$store.state.t('Index')"
          ></v-text-field>
          <v-textarea
                  v-model="address"
                  :error-messages="addressErrors"
                  :label="$store.state.t('Address')"
                  required
                  @input="$v.address.$touch()"
                  @blur="$v.address.$touch()"
          ></v-textarea>
          <v-textarea
                  v-model="notice"
                  :label="$store.state.t('Notice')"
          ></v-textarea>

          <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
          <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';
  import flag from "../../components/flag";
  import {CM} from "../../../managers/ContractorsManager";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      flag,
      CM
    },

    mixins: [validationMixin],

    validations: {
      address: { required, maxLength: maxLength(250) },
      address_type_id: { required },
      contractor_id: { required },
    },

    data () {
      return {
        contractorManager:null,

        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        address: '',
        index: '',
        notice: '',
        address_type_id: null,
        address_typesItems: [],
        contractor_id: null,
        contractor_Items: [],
        city_id: null,
        cities_Items: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {
      this.contractorManager = new CM();

      this.getAddressTypes();
      this.getCities();
      this.getContractors();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/addresses/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.address = response.data.address;
                    this.index = response.data.index;
                    this.notice = response.data.notice;
                    this.address_type_id = response.data.address_type_id;
                    this.city_id = response.data.city_id;
                    this.contractor_id = response.data.contractor_id;
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
      getAddressTypes: function () {
        axios.get(window.apiDomainUrl+'/address-types/get-all-for-select', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.address_typesItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getContractors: function () {
        this.contractorManager.getForSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.contractor_Items = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getCities: function(){
        axios.get(window.apiDomainUrl+'/cities/get-all-for-select', qs.stringify({}))
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
          address: this.address,
          index: this.index,
          notice: this.notice,
          address_type_id: this.address_type_id,
          city_id: this.city_id,
          contractor_id: this.contractor_id,
        };

        axios.post(window.apiDomainUrl+'/addresses/create', qs.stringify(createData))
                .then( (response) => {
                  if (response.data !== false){
                    this.$eventHub.$emit(this.updateItemListNameTrigger);
                    this.showDialog = false;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          address: this.address,
          index: this.index,
          notice: this.notice,
          address_type_id: this.address_type_id,
          city_id: this.city_id,
          contractor_id: this.contractor_id,
          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/addresses/update', qs.stringify(updateData))
                .then( (response) => {
                  if (response.data !== false){
                    this.$eventHub.$emit(this.updateItemListNameTrigger);
                    this.showDialog = false;
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
        this.address = '';
        this.index = '';
        this.notice = '';
        this.address_type_id = null;
        this.city_id = null;
        this.contractor_id = null;
        this.rowId = 0;
      }
    },

    computed: {
      addressErrors () {
        const errors = [];
        if (!this.$v.address.$dirty) return errors
        !this.$v.address.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      address_type_idErrors () {
        const errors = [];
        if (!this.$v.address_type_id.$dirty) return errors
        !this.$v.address_type_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      contractor_idErrors () {
        const errors = [];
        if (!this.$v.contractor_id.$dirty) return errors
        !this.$v.contractor_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>