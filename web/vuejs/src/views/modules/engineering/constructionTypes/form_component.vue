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
              :label="$store.state.t('Construction Type')"
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
                  required
                  @input="$v.country_id.$touch()"
                  @blur="$v.country_id.$touch()"
          ></v-autocomplete>

          <v-text-field
              v-model="notice"
              :counter="250"
              :label="$store.state.t('Notice')"
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
  import {ConstructionTypesManager} from "@/managers/ConstructionTypesManager";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";
  import {CountriesManager} from "@/managers/CountriesManager";
  import loadercustom from "../../../components/loadercustom";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom
    },

    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(250) },
      country_id: { required },
    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',

        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        name: '',
        notice: null,


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

      this.constructionTypesManager = new ConstructionTypesManager();
      this.countriesManager = new CountriesManager();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.constructionTypesManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.country_id = response.data.country_id;
                    this.notice = response.data.notice;
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
      initFormComponent: function(){
        this.getCountriesForSelect();
      },
      getCountriesForSelect: function () {
        this.countriesManager.getAllForSelect()
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
          country_id: this.country_id,
          notice: this.notice
        };

        this.constructionTypesManager.create(createData)
                  .then( (response) => {
                    if (response.data !== false){
                      if (response.data.error) {
                        this.showCustomLoaderDialog = true;
                        this.customDialogfrontString = this.$store.state.t(response.data.error);
                        setTimeout(() => {
                          this.showCustomLoaderDialog = false;
                        }, 5000);
                      } else {
                        this.$eventHub.$emit(this.updateItemListNameTrigger);
                        this.showDialog = false;
                      }
                    }
                  })
                .catch(function (error) {
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          name: this.name,
          country_id: this.country_id,
          notice: this.notice,
          id: this.rowId
        };

        this.constructionTypesManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    if (response.data.error) {
                      this.showCustomLoaderDialog = true;
                      this.customDialogfrontString = this.$store.state.t(response.data.error);
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, 5000);
                    } else {
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
                      this.setDefaultData();
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
        this.notice = null;
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
