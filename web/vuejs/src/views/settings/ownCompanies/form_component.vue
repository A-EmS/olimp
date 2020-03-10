<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
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
                  required
                  @input="$v.country_id.$touch()"
                  @blur="$v.country_id.$touch()"
                  @change="onCountryChange"
          ></v-autocomplete>
          <v-autocomplete
                  v-model="entity_id"
                  :error-messages="entity_idErrors"
                  :items="entitiesItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Entity')"
                  required
                  @input="$v.country_id.$touch()"
                  @blur="$v.country_id.$touch()"
          ></v-autocomplete>
          <v-textarea
                  v-model="notice"
                  :label="$store.state.t('Notice')"
          ></v-textarea>
          <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
          <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>

    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';

  import loadercustom from "../../components/loadercustom";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";
  import {CountriesManager} from "../../../managers/CountriesManager";
  import {EM} from "../../../managers/EntitiesManager";
  import {OwnCompaniesManager} from "../../../managers/OwnCompaniesManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom
    },

    mixins: [validationMixin],

    validations: {
      country_id: { required },
      entity_id: { required },
    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',
        confirmDeleteString: '',
        showConfirmatorDialog: false,

        showDialog: false,
        valid: true,
        rowId: 0,
        notice: '',

        country_id: null,
        countryItems: [],
        entity_id: null,
        entitiesItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {
      this.entitiesManager = new EM();
      this.countriesManager = new CountriesManager();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.getCountriesForSelect();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
            this.ownCompaniesManager.get(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.country_id = response.data.country_id;
                    this.entity_id = response.data.entity_id;
                    this.notice = response.data.notice;

                    this.onCountryChange();
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
        this.countriesManager.getForSelectAccordingEntities()
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
        this.entitiesManager.getEntitiesByCountryId(this.country_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.entitiesItems = response.data.items;
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
          entity_id: this.entity_id,
          notice: this.notice
        };
        this.ownCompaniesManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (response.data.error) {
                      this.showCustomLoaderDialog = true;
                      this.customDialogfrontString = this.$store.state.t('Creating was no happened. Perhaps you have already have company with selected entity.');
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
          entity_id: this.entity_id,
          notice: this.notice,
          id: this.rowId
        };

        this.ownCompaniesManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    this.$eventHub.$emit(this.updateItemListNameTrigger);
                    this.showDialog = false;
                  }
                })
                .catch((error) => {
                  this.showCustomLoaderDialog = true;
                  this.customDialogfrontString = this.$store.state.t('Updating was no happened. Perhaps you have already have company with selected entity.');
                  setTimeout(() => {
                    this.showCustomLoaderDialog = false;
                  }, 5000);
                  console.log(error);
                });
      },
      cancel () {
        this.$v.$reset();
        this.showDialog = false;
      },

      setDefaultData () {
        this.notice = '';
        this.entity_id = null;
        this.country_id = null;
        this.entitiesItems = [];
        this.rowId = 0;
      }
    },

    computed: {
      country_idErrors () {
        const errors = []
        if (!this.$v.country_id.$dirty) return errors
        !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      entity_idErrors () {
        const errors = []
        if (!this.$v.entity_id.$dirty) return errors
        !this.$v.entity_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
