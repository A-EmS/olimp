<template>
      <div class="col-md-12 col-lg-6 col-xl-6">
        <b-card class="mb-3 nav-justified" no-body>
          <b-tabs class="card-header-tab-animation" card>
            <b-tab :title="$store.state.t('Currency Exchange')" active v-on:click="toggleShowingCurrencyExchange()">
              <div v-if="showCurrencyExchange">
                <b-card :title="$store.state.t('Available to exchanging')" class="main-card mb-4">
                  <b-row class="mb-3">
                    <b-col v-for="availableCurrency in currencyItemsWithBalances" md="3" class="my-1" style="color: grey">
                      <span style="font-weight: bold">{{availableCurrency.currency_name}}: </span> <span style="color: green;">{{numberFormatThousandsSpace(availableCurrency.amount)}} {{availableCurrency.currency_short_name}} </span>
                    </b-col>
                  </b-row>
                </b-card>
                <div>
                  <v-select
                      v-model="currency_to_sell_id"
                      :error-messages="currency_to_sell_idErrors"
                      :items="currencyItemsWithBalances"
                      item-value="currency_id"
                      item-text="currency_name"
                      :label="$store.state.t('Currency To Sell')"
                      @change="onChangeCurrencyToSellId"
                      required
                      @input="$v.currency_to_sell_id.$touch()"
                      @blur="$v.currency_to_sell_id.$touch()"
                  ></v-select>
                  <v-select
                      v-model="currency_to_buy_id"
                      :error-messages="currency_to_buy_idErrors"
                      :items="currencyItems"
                      item-value="id"
                      item-text="currency_name"
                      :label="$store.state.t('Currency To Buy')"
                      required
                      @input="$v.currency_to_buy_id.$touch()"
                      @blur="$v.currency_to_buy_id.$touch()"
                  ></v-select>
                  <v-text-field
                      v-model="amount_to_sell"
                      :error-messages="amount_to_sellErrors"
                      :label="$store.state.t('I will sell')"
                      required
                      type="number"
                      step="0.01"
                      @keyup="calculateAmountToBuy"
                      @change="calculateAmountToBuy"
                      @input="$v.amount_to_sell.$touch()"
                      @blur="$v.amount_to_sell.$touch()"
                  ></v-text-field>
                  <v-text-field
                      v-model="exchange_rate"
                      :error-messages="exchange_rateErrors"
                      :label="$store.state.t('Exchange Rate')"
                      required
                      type="number"
                      step="0.01"
                      @keyup="calculateAmountToBuy"
                      @change="calculateAmountToBuy"
                      @input="$v.exchange_rate.$touch()"
                      @blur="$v.exchange_rate.$touch()"
                  ></v-text-field>
                  <v-text-field
                      v-model="amount_to_buy"
                      :error-messages="amount_to_buyErrors"
                      :label="$store.state.t('I will get')"
                      value="0"
                      readonly="readonly"
                      @input="$v.amount_to_buy.$touch()"
                      @blur="$v.amount_to_buy.$touch()"
                  ></v-text-field>
                  <v-textarea
                      v-model="notice"
                      :label="$store.state.t('Notice')"
                  ></v-textarea>
                  <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                </div>
              </div>
            </b-tab>
          </b-tabs>
        </b-card>

        <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
      </div>
</template>

<script>

import PageTitle from "../../../../Layout/Components/PageTitle.vue";
import loadercustom from "../../../components/loadercustom";
import form_component from "./form_component";
var moment = require('moment');

import qs from "qs";
import axios from "axios";
import accessMixin from "../../../../mixins/accessMixin";
import {OrdersManager} from "../../../../managers/OrdersManager";
import {CurrenciesManager} from "../../../../managers/CurrenciesManager";
import Options from "../../../../DemoPages/Vuetify/Components/scrolling/options";
import mathMixin from "@/mixins/mathMixin";
import {TillsManager} from "@/managers/TillsManager";
import {validationMixin} from "vuelidate";
import {minValue, required} from "vuelidate/lib/validators";

export default {
  components: {
    Options,
    PageTitle,
    loadercustom,
    form_component,
    moment,
  },

  mixins: [accessMixin, mathMixin, validationMixin],

  validations: {
    currency_to_sell_id: { required },
    currency_to_buy_id: { required },
    amount_to_sell: { required, minValue: minValue(0.01) },
    amount_to_buy: { required },
    exchange_rate: { required, minValue: minValue(0.00001) },
  },

  data: () => ({

    showCustomLoaderDialog: false,
    customDialogfrontString: 'Please stand by',
    showConfirmatorDialog: false,
    showCurrencyExchange: false,

    currency_to_sell_id: null,
    currency_to_buy_id: null,
    amount_to_sell: 0.01,
    amount_to_buy: 0.01,
    exchange_rate: 1,
    maxAmount: 99999999,
    notice: '',

    tillsItems: [],
  }),

  created: function() {
    this.currenciesManager = new CurrenciesManager();
    this.ordersManager = new OrdersManager();
    this.tillsManager = new TillsManager();
  },
  props: {
    currencyItems: {type: Array, require: true, default: []},
    currencyItemsWithBalances: {type: Array, require: true, default: []},
  },

  methods: {
    calculateAmountToBuy: function (){

      this.amount_to_sell = this.round(this.amount_to_sell, 2);
      this.exchange_rate = this.round(this.exchange_rate, 3);

      this.amount_to_buy = this.round((this.amount_to_sell * this.exchange_rate), 2);
    },
    onChangeCurrencyToSellId: function(){
      this.amount_to_sell = 0.01;

      var balance = this.currencyItemsWithBalances.find(obj => obj.currency_id == this.currency_to_sell_id);

      if (typeof balance !== 'undefined') {
        this.maxAmount = balance.amount;
      }
      this.calculateAmountToBuy();
      return this.maxAmount;
    },
    getMaxAmount: function (){
      return this.maxAmount;
    },
    submit: function () {

      this.$v.$touch();
      if (!this.$v.$invalid) {
        this.create();
      }
    },
    create: function(){

      if (this.amount > this.getMaxAmount()){
        return;
      }

      var createData = {
        currency_to_sell_id: this.currency_to_sell_id,
        currency_to_buy_id: this.currency_to_buy_id,
        amount_to_sell: this.amount_to_sell,
        amount_to_buy: this.amount_to_buy,
        notice: this.notice,
      };

      this.ordersManager.createCurrencyExchange(createData)
          .then( (response) => {
            if (response.data !== false){
              if (!response.data.error){
                this.$router.go(this.$router.currentRoute);
              } else {
                this.openErrorDialog(response.data.error);
              }
            }
          })
          .catch(function (error) {
            console.log(error);
          });
    },
    toggleShowingCurrencyExchange: function(){
      this.showCurrencyExchange = !this.showCurrencyExchange;
    },
  },

  computed: {
    currency_to_sell_idErrors () {
      const errors = []
      if (!this.$v.currency_to_sell_id.$dirty) return errors
      !this.$v.currency_to_sell_id.required && errors.push(this.$store.state.t('Required field'))
      return errors
    },
    currency_to_buy_idErrors () {
      const errors = []
      if (!this.$v.currency_to_buy_id.$dirty) return errors
      !this.$v.currency_to_buy_id.required && errors.push(this.$store.state.t('Required field'))
      return errors
    },

    exchange_rateErrors () {
      const errors = []

      if (!this.$v.exchange_rate.$dirty) return errors
      !this.$v.exchange_rate.required && errors.push(this.$store.state.t('Required field'))
      !this.$v.exchange_rate.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.00001'))
      return errors
    },
    amount_to_sellErrors () {
      const errors = []

      if (this.amount_to_sell > this.getMaxAmount()) {
        errors.push(this.$store.state.t('Max value has to be less or equal ' + this.getMaxAmount()))
        return errors
      }

      if (!this.$v.amount_to_sell.$dirty) return errors
      !this.$v.amount_to_sell.required && errors.push(this.$store.state.t('Required field'))
      !this.$v.amount_to_sell.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
      return errors
    },
    amount_to_buyErrors () {
      const errors = []

      if (!this.$v.amount_to_buy.$dirty) return errors
      !this.$v.amount_to_buy.required && errors.push(this.$store.state.t('Required field'))
      return errors
    },
  },

  beforeDestroy () {

  },
}
</script>