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
          <v-autocomplete
                  v-model="currency_id"
                  :error-messages="currency_idErrors"
                  :items="currencies_Items"
                  item-value="id"
                  item-text="currency_name"
                  :label="$store.state.t('Currency')"
                  required
                  @input="$v.currency_id.$touch()"
                  @blur="$v.currency_id.$touch()"
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
  import { required } from 'vuelidate/lib/validators'
  import {CurrenciesManager} from "../../../managers/CurrenciesManager";
  import {PriceListsManager} from "../../../managers/PriceListsManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {
      name: { required },
      currency_id: { required },
    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',
        confirmDeleteString: '',
        showConfirmatorDialog: false,

        showDialog: false,
        valid: true,
        header: '',

        rowId: 0,

        name: null,
        notice: null,
        currencies_Items: [],
        currency_id: null,
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {
      this.priceListsManager = new PriceListsManager();
      this.currenciesManager = new CurrenciesManager();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.priceListsManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.currency_id = response.data.currency_id;
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
        this.getCurrenciesForSelect();
      },
      getCurrenciesForSelect: function () {
        this.currenciesManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.currencies_Items = response.data.items;
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
          notice: this.notice,
          currency_id: this.currency_id
        };

        this.priceListsManager.create(createData)
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
                .catch((error) => {
                  this.showCustomLoaderDialog = true;
                  this.customDialogfrontString = this.$store.state.t(error);
                  setTimeout(() => {
                    this.showCustomLoaderDialog = false;
                  }, 5000);
                  this.setDefaultData();
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          name: this.name,
          notice: this.notice,
          currency_id: this.currency_id,
          id: this.rowId
        };

        this.priceListsManager.update(updateData)
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
                .catch((error) => {
                  this.showCustomLoaderDialog = true;
                  this.customDialogfrontString = this.$store.state.t(error);
                  setTimeout(() => {
                    this.showCustomLoaderDialog = false;
                  }, 5000);
                  console.log(error);
                  this.setDefaultData();
                });
      },
      cancel () {
        this.$v.$reset();
        this.showDialog = false;
      },

      setDefaultData () {
        this.notice = null;
        this.name = null;
        this.currency_id = null;
        this.rowId = 0;
      }
    },

    computed: {
      currency_idErrors () {
        const errors = [];
        if (!this.$v.currency_id.$dirty) return errors;
        !this.$v.currency_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      nameErrors () {
        const errors = [];
        if (!this.$v.name.$dirty) return errors;
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
