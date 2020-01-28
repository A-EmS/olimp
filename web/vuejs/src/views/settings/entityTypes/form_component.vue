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
                  :label="$store.state.t('Name')"
                  required
                  @input="$v.name.$touch()"
                  @blur="$v.name.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="short_name"
                  :error-messages="short_nameErrors"
                  :counter="250"
                  :label="$store.state.t('Full Name')"
                  required
                  @input="$v.short_name.$touch()"
                  @blur="$v.short_name.$touch()"
          ></v-text-field>
          <v-select
                  v-model="country_id"
                  :error-messages="country_idErrors"
                  :items="countryItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Country')"
                  required
                  @input="$v.country_id.$touch()"
                  @blur="$v.country_id.$touch()"
          ></v-select>
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
      short_name: { required, maxLength: maxLength(250) },
      country_id: { required },
    },

    data () {
      return {
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        name: '',
        short_name: '',
        notice: '',
        country_id: null,
        countryItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.getCountriesForSelect();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/entity-types/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.short_name = response.data.short_name;
                    this.notice = response.data.notice;
                    this.country_id = response.data.country_id;
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
      getCountriesForSelect: function () {
        axios.get(window.apiDomainUrl+'/countries/get-all-for-select', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.countryItems = response.data.items;
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
          short_name: this.short_name,
          notice: this.notice,
          country_id: this.country_id
        };

        axios.post(window.apiDomainUrl+'/entity-types/create', qs.stringify(createData))
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
          short_name: this.short_name,
          notice: this.notice,
          country_id: this.country_id,
          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/entity-types/update', qs.stringify(updateData))
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
        this.short_name = '';
        this.notice = '';
        this.country_id = null;
        this.rowId = 0;
      }
    },

    computed: {
      nameErrors () {
        const errors = []
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push(this.$store.state.t('Name must be at most 250 characters long'))
        !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      short_nameErrors () {
        const errors = []
        if (!this.$v.short_name.$dirty) return errors
        !this.$v.short_name.maxLength && errors.push(this.$store.state.t('Full Name must be at most 250 characters long'))
        !this.$v.short_name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      country_idErrors () {
        const errors = []
        if (!this.$v.country_id.$dirty) return errors
        !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
