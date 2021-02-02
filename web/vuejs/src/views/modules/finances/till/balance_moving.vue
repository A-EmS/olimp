<template>
      <div class="col-md-12 col-lg-6 col-xl-6">
        <b-card class="mb-3 nav-justified" no-body>
          <b-tabs class="card-header-tab-animation" card>
            <b-tab :title="$store.state.t('Balance Moving')" active v-on:click="toggleShowingBalanceMoving()">
              <div v-if="showBalanceMoving">
                <b-card :title="$store.state.t('Available to moving')" class="main-card mb-4">
                  <b-row class="mb-3">
                    <b-col v-for="availableCurrency in currencyItemsWithBalances" md="3" class="my-1" style="color: grey">
                      <span style="font-weight: bold">{{availableCurrency.currency_name}}: </span> <span style="color: green;">{{availableCurrency.amount}} {{availableCurrency.currency_short_name}} </span>
                    </b-col>
                  </b-row>
                </b-card>
                <div>
                  <v-select
                      v-model="target_till_id"
                      :error-messages="target_till_idErrors"
                      :items="tillsItems"
                      item-value="id"
                      item-text="name"
                      :label="$store.state.t('Target Till')"
                      required
                      @input="$v.target_till_id.$touch()"
                      @blur="$v.target_till_id.$touch()"
                  ></v-select>
                  <v-select
                      v-model="currency_id"
                      :error-messages="currency_idErrors"
                      :items="currencyItemsWithBalances"
                      item-value="currency_id"
                      item-text="currency_name"
                      :label="$store.state.t('Currency To Move')"
                      @change="onChangeCurrencyId"
                      required
                      @input="$v.currency_id.$touch()"
                      @blur="$v.currency_id.$touch()"
                  ></v-select>
                  <v-text-field
                      v-model="amount"
                      :error-messages="amountErrors"
                      :label="$store.state.t('I Will Move')"
                      required
                      type="number"
                      min="0"
                      step="0.01"
                      @input="$v.amount.$touch()"
                      @blur="$v.amount.$touch()"
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

import accessMixin from "../../../../mixins/accessMixin";
import {OrdersManager} from "../../../../managers/OrdersManager";
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
    target_till_id: { required },
    currency_id: { required },
    amount: { required, minValue: minValue(0.01) },
  },

  data: () => ({
    showCustomLoaderDialog: false,
    customDialogfrontString: 'Please stand by',
    showConfirmatorDialog: false,
    showBalanceMoving: false,

    target_till_id: null,
    currency_id: null,
    amount: null,
    notice: '',
    maxAmount: 99999999,

    tillsItems: [],
  }),

  created: function() {
    this.ordersManager = new OrdersManager();
    this.tillsManager = new TillsManager();

    this.getTills();
  },

  props: {
    currencyItemsWithBalances: {type: Array, require: true, default: []},
  },

  methods: {
    toggleShowingBalanceMoving: function(){
      this.showBalanceMoving = !this.showBalanceMoving;
    },

    getTills: function (){
      this.tillsManager.getAllExceptUserTill()
          .then( (response) => {
            if(response.data !== false){
              this.tillsItems = response.data.items;
            }
          })
          .catch(function (error) {
            console.log(error);
          });
    },
    onChangeCurrencyId: function(){
      this.amount = 0.01;

      var balance = this.currencyItemsWithBalances.find(obj => obj.currency_id == this.currency_id);

      if (typeof balance !== 'undefined') {
        this.maxAmount = balance.amount;
      }

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
        target_till_id: this.target_till_id,
        currency_id: this.currency_id,
        amount: this.amount,
        notice: this.notice,
      };

      this.ordersManager.createBalanceMoving(createData)
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
  },

  computed: {
    target_till_idErrors () {
      const errors = []
      if (!this.$v.target_till_id.$dirty) return errors
      !this.$v.target_till_id.required && errors.push(this.$store.state.t('Required field'))
      return errors
    },
    currency_idErrors () {
      const errors = []
      if (!this.$v.currency_id.$dirty) return errors
      !this.$v.currency_id.required && errors.push(this.$store.state.t('Required field'))
      return errors
    },
    amountErrors () {
      const errors = []

      if (this.amount > this.getMaxAmount()) {
        errors.push(this.$store.state.t('Max value has to be less or equal ' + this.getMaxAmount()))
        return errors
      }

      if (!this.$v.amount.$dirty) return errors
      !this.$v.amount.required && errors.push(this.$store.state.t('Required field'))
      !this.$v.amount.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
      return errors
    },
  },

  beforeDestroy () {

  },
}
</script>