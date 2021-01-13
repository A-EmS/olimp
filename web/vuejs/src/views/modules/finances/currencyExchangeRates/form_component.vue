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
              <v-autocomplete
                  v-model="currency_id_base"
                  :error-messages="currency_id_baseErrors"
                  :items="currencyItems"
                  item-value="id"
                  item-text="currency_name"
                  :label="$store.state.t('Currency Base')"
                  @change="onCurrencyChange('base')"
              ></v-autocomplete>
              <v-text-field
                  v-model="rate_base"
                  :error-messages="rate_baseErrors"
                  :counter="250"
                  :label="$store.state.t('Rate Base')"
                  required
                  type="number"
                  min="0.001"
                  step="0.001"
                  @input="$v.rate_base.$touch()"
                  @blur="onChangeRate('base') && $v.rate_base.$touch()"
              ></v-text-field>
              <v-autocomplete
                  v-model="currency_id_ref"
                  :error-messages="currency_id_refErrors"
                  :items="currencyItems"
                  item-value="id"
                  item-text="currency_name"
                  :label="$store.state.t('Currency Ref')"
                  @change="onCurrencyChange"
              ></v-autocomplete>
              <v-text-field
                  v-model="rate_ref"
                  :error-messages="rate_refErrors"
                  :counter="250"
                  :label="$store.state.t('Rate Ref')"
                  required
                  type="number"
                  min="0.001"
                  step="0.001"
                  @input="$v.rate_ref.$touch()"
                  @blur="onChangeRate() && $v.rate_ref.$touch()"
              ></v-text-field>
              <v-flex xs12 sm12 md12>
                <v-menu
                    v-model="dateMenuFrom"
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
                        :readonly="!editDateAccess && rowId > 0"
                        v-model="date_from"
                        :error-messages="date_fromErrors"
                        :label="$store.state.t('Date From')"
                        prepend-icon="event"
                        required
                        v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker @change="onDateFromChange" v-if="rowId === 0 || editDateAccess" v-model="date_from" @input="dateMenuFrom = false"></v-date-picker>
                </v-menu>
              </v-flex>
              <v-checkbox
                  v-model="editDateAccess"
                  @change="onEditDateAccess"
                  :label="$store.state.t('get access to change periods manually, and notice(if editing) because i understand what i am going to do (the fact of this action will be noted)')"
              ></v-checkbox>
              <v-flex xs12 sm12 md12>
                <v-menu
                    v-model="dateMenuTo"
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
                        :readonly="!editDateAccess"
                        v-model="date_to"
                        :error-messages="date_toErrors"
                        :label="$store.state.t('Date To')"
                        prepend-icon="event"
                        required
                        v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-if="editDateAccess" :min=miDatePickerValue v-model="date_to" @input="dateMenuTo = false"></v-date-picker>
                </v-menu>
              </v-flex>
              {{manuallyChange}}
                <v-text-field
                    v-model="notice"
                    :label="$store.state.t('Notice')"
                    :counter="250"
                    :readonly="!editDateAccess && rowId > 0"
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

  import LayoutWrapper from '../../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../../Layout/Components/DemoCard';

  import { validationMixin } from 'vuelidate'
  import {minValue, required} from 'vuelidate/lib/validators'
  import loadercustom from "../../../components/loadercustom";
  import {CurrenciesManager} from "@/managers/CurrenciesManager";
  import constantsMixin from "@/mixins/constantsMixin";
  import {CurrencyExchangeRatesManager} from "@/managers/CurrencyExchangeRatesManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin, constantsMixin],

    validations: {
      currency_id_base: { required },
      currency_id_ref: { required },
      date_from: { required },
      rate_base: { required, minValue: minValue(0.001) },
      rate_ref: { required, minValue: minValue(0.001) },
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
        dateMenuFrom: false,
        dateMenuTo: false,
        miDatePickerValue: '',

        editDateAccess: false,
        manuallyChange: '',

        currency_id_base: null,
        currency_id_ref: null,
        rate_base: null,
        rate_ref: null,
        date_from: null,
        date_to: null,
        notice: '',

        currencyItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {
      this.currenciesManager = new CurrenciesManager();
      this.currencyExchangeRatesManager = new CurrencyExchangeRatesManager();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.initFormComponent();
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.rowId = 0;
        this.currencyExchangeRatesManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.currency_id_base = response.data.currency_id_base;
                    this.currency_id_ref = response.data.currency_id_ref;
                    this.date_from = response.data.date_from;
                    this.date_to = response.data.date_to;
                    this.rate_ref = response.data.rate_ref;
                    this.rate_base = response.data.rate_base;
                    this.notice = response.data.notice;
                    this.editDateAccess = false;
                    this.manuallyChange = '';
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.showDialog = true;
      });

    },

    methods: {
      initFormComponent: function(){
          this.getCurrencies();
      },
      onEditDateAccess: function () {
        this.manuallyChange = '(MANUALLY CHANGE!) ';

        // this.date_to = null;
      },
      onDateFromChange: function () {
        this.miDatePickerValue = this.date_from;
      },
      onCurrencyChange: function(type){
          if (type === 'base') {
            if (this.currency_id_ref === this.currency_id_base){
              this.currency_id_ref = null;
            }
          } else {
            if (this.currency_id_ref === this.currency_id_base){
              this.currency_id_base = null;
            }
          }
      },
      onChangeRate: function(type){
          if (type === 'base') {
            this.rate_ref = Math.round((1/this.rate_base)*10000)/10000;
          } else {
            this.rate_base = Math.round((1/this.rate_ref)*10000)/10000;
          }
        },
        getCurrencies: function() {
          this.currenciesManager.getAll()
            .then( (response) => {
              if(response.data !== false){
                this.currencyItems = response.data.items;
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
          currency_id_base: this.currency_id_base,
          currency_id_ref: this.currency_id_ref,
          date_from: this.date_from,
          date_to: this.date_to,
          rate_base: this.rate_base,
          rate_ref: this.rate_ref,
          notice: this.manuallyChange + this.notice
        };

        this.currencyExchangeRatesManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                        window.j('html, body').animate({scrollTop: 0}, 400);
                        this.rowId = response.data.id;
                        this.showDialog = false;
                    } else {
                      this.openErrorDialog(response.data.error);
                    }
                  }
                }).catch(function (error) {
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          currency_id_base: this.currency_id_base,
          currency_id_ref: this.currency_id_ref,
          date_from: this.date_from,
          date_to: this.date_to,
          rate_base: this.rate_base,
          rate_ref: this.rate_ref,
          notice: this.manuallyChange + this.notice,
          id: this.rowId
        };

        this.currencyExchangeRatesManager.update(updateData)
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
        this.editDateAccess = false;
        this.currency_id_base = null;
        this.currency_id_ref = null;
        this.date_from = null;
        this.date_to = null;
        this.rate_base = null;
        this.rate_ref = null;
        this.notice = '';
        this.manuallyChange = '';

        this.miDatePickerValue = '';
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
      dateErrors () {
        const errors = []
        if (!this.$v.date.$dirty) return errors
        !this.$v.date.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      currency_id_baseErrors () {
        const errors = []
        if (!this.$v.currency_id_base.$dirty) return errors
        !this.$v.currency_id_base.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      currency_id_refErrors () {
        const errors = []
        if (!this.$v.currency_id_ref.$dirty) return errors
        !this.$v.currency_id_ref.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      rate_baseErrors () {
        const errors = []
        if (!this.$v.rate_base.$dirty) return errors
        !this.$v.rate_base.required && errors.push(this.$store.state.t('Required field'))
        !this.$v.rate_base.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
        return errors
      },
      rate_refErrors () {
        const errors = []
        if (!this.$v.rate_ref.$dirty) return errors
        !this.$v.rate_ref.required && errors.push(this.$store.state.t('Required field'))
        !this.$v.rate_ref.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
        return errors
      },
      date_fromErrors () {
        const errors = []
        if (!this.$v.date_from.$dirty) return errors
        !this.$v.date_from.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      date_toErrors () {
        const errors = []
        // if (!this.$v.date_to.$dirty) return errors
        // !this.$v.date_to.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
