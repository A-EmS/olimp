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
                  v-model="name"
                  :error-messages="nameErrors"
                  :counter="250"
                  label="Name"
                  required
                  @input="$v.name.$touch()"
                  @blur="$v.name.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="full_name"
                  :error-messages="full_nameErrors"
                  :counter="250"
                  label="Full Name"
                  required
                  @input="$v.full_name.$touch()"
                  @blur="$v.full_name.$touch()"
          ></v-text-field>
          <v-select
                  v-model="flag_code"
                  :error-messages="flag_codeErrors"
                  :items="flag_codeItems"
                  item-value="code"
                  label="Flag"
                  required
                  @input="$v.flag_code.$touch()"
                  @blur="$v.flag_code.$touch()"
          >
            <template slot="selection" slot-scope="row">
              <flag :country-acronym="flag_code"></flag>
            </template>
            <template slot="item" slot-scope="row">
              <flag :country-acronym="row.item.code"></flag> &nbsp; - {{ row.item.code }}
            </template>
          </v-select>
          <v-select
                  v-model="world_parts_id"
                  :error-messages="world_parts_idErrors"
                  :items="world_partItems"
                  item-value="id"
                  item-text="name"
                  label="World Part"
                  required
                  @input="$v.world_parts_id.$touch()"
                  @blur="$v.world_parts_id.$touch()"
          ></v-select>
          <v-text-field
                  v-model="alpha2"
                  :error-messages="alpha2Errors"
                  :counter="2"
                  label="Alpha2"
                  required
                  @input="$v.alpha2.$touch()"
                  @blur="$v.alpha2.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="alpha3"
                  :error-messages="alpha3Errors"
                  :counter="3"
                  label="Alpha3"
                  required
                  @input="$v.alpha3.$touch()"
                  @blur="$v.alpha3.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="iso"
                  :error-messages="isoErrors"
                  :counter="250"
                  label="ISO"
                  required
                  @input="$v.iso.$touch()"
                  @blur="$v.iso.$touch()"
          ></v-text-field>
          <v-textarea
                  v-model="location"
                  label="Location"
          ></v-textarea>

          <v-btn color="success" @click="submit">submit</v-btn>
          <v-btn  @click="cancel">cancel</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';
  import flag from "../../components/flag";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      flag
    },

    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(250) },
      full_name: { required, maxLength: maxLength(250) },
      alpha2: { required, maxLength: maxLength(2) },
      alpha3: { required, maxLength: maxLength(3) },
      iso: { required },
      flag_code: { required },
      world_parts_id: { required },
    },

    data () {
      return {
        showDialog: false,
        valid: true,
        header: 'Action...',
        rowId: 0,
        name: '',
        full_name: '',
        alpha2: '',
        alpha3: '',
        iso: '',
        location: '',
        flag_code: null,
        flag_codeItems: [],
        world_parts_id: null,
        world_partItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.getWorldPartsForCountries();
      this.getCountryFlags();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = 'Creating new...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/countries/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.full_name = response.data.full_name;
                    this.alpha2 = response.data.alpha2;
                    this.alpha3 = response.data.alpha3;
                    this.iso = response.data.iso;
                    this.location = response.data.location;
                    this.flag_code = response.data.flag_code;
                    this.world_parts_id = response.data.world_parts_id;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.header = 'Updating...';
        this.showDialog = true;
      });

    },

    methods: {
      getWorldPartsForCountries: function () {
        axios.get(window.apiDomainUrl+'/world-parts/get-all-for-countries', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.world_partItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getCountryFlags: function () {
        axios.get(window.apiDomainUrl+'/languages/get-flags', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.flag_codeItems = response.data;
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
          full_name: this.full_name,
          alpha2: this.alpha2,
          alpha3: this.alpha3,
          iso: this.iso,
          location: this.location,
          flag_code: this.flag_code,
          world_parts_id: this.world_parts_id
        };

        axios.post(window.apiDomainUrl+'/countries/create', qs.stringify(createData))
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
          name: this.name,
          full_name: this.full_name,
          alpha2: this.alpha2,
          alpha3: this.alpha3,
          iso: this.iso,
          location: this.location,
          flag_code: this.flag_code,
          world_parts_id: this.world_parts_id,
          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/countries/update', qs.stringify(updateData))
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
        this.name = '';
        this.full_name = '';
        this.alpha2 = '';
        this.alpha3 = '';
        this.iso = '';
        this.location = '';
        this.flag_code = null;
        this.world_parts_id = null;
        this.rowId = 0;
      }
    },

    computed: {
      nameErrors () {
        const errors = []
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push('Name must be at most 250 characters long')
        !this.$v.name.required && errors.push('Name is required.')
        return errors
      },
      full_nameErrors () {
        const errors = []
        if (!this.$v.full_name.$dirty) return errors
        !this.$v.full_name.maxLength && errors.push('Full Name must be at most 250 characters long')
        !this.$v.full_name.required && errors.push('Full Name is required.')
        return errors
      },
      alpha2Errors () {
        const errors = []
        if (!this.$v.alpha2.$dirty) return errors
        !this.$v.alpha2.maxLength && errors.push('alpha2 must be at most 2 characters long')
        !this.$v.alpha2.required && errors.push('alpha2 is required.')
        return errors
      },
      alpha3Errors () {
        const errors = []
        if (!this.$v.alpha3.$dirty) return errors
        !this.$v.alpha3.maxLength && errors.push('alpha3 must be at most 3 characters long')
        !this.$v.alpha3.required && errors.push('alpha3 is required.')
        return errors
      },
      isoErrors () {
        const errors = []
        if (!this.$v.iso.$dirty) return errors
        !this.$v.iso.required && errors.push('iso is required.')
        return errors
      },
      flag_codeErrors () {
        const errors = []
        if (!this.$v.flag_code.$dirty) return errors
        !this.$v.flag_code.required && errors.push('Flag is required.')
        return errors
      },
      world_parts_idErrors () {
        const errors = []
        if (!this.$v.world_parts_id.$dirty) return errors
        !this.$v.world_parts_id.required && errors.push('World Part is required.')
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
