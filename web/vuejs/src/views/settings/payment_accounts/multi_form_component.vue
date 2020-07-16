<template>
    <div>
        <b-button class="mr-2 mb-2 btn-pill" variant="success" @click="addToPull()">{{$store.state.t('Add Payment Account')}}</b-button>
        <br />

        <b-tabs card>
          <b-tab :title="$store.state.t('Payment Account')" v-for="acc in pullPaymentAccounts">
              <v-autocomplete
                      v-model="acc.country_id"
                      :items="countryItems"
                      item-value="id"
                      item-text="name"
                      :label="$store.state.t('Country')"
                      required
                      @change="onCountryChange(acc)"
              ></v-autocomplete>
            <v-autocomplete
                    v-model="acc.bank_id"
                    :items="banks_Items"
                    item-value="id"
                    item-text="bank_full_search_info"
                    :label="$store.state.t('Bank') + ' / ' + $store.state.t('IBAN') + ' / ' + $store.state.t('Payment Account')"
                    @change="onBankChange(acc)"
            ></v-autocomplete>
            <v-autocomplete
                    v-model="acc.currency_id"
                    :items="currencies_Items"
                    item-value="id"
                    item-text="currency_name"
                    :label="$store.state.t('Currency')"
            ></v-autocomplete>
            <v-text-field
                    v-model="acc.iban"
                    :disabled="acc.bank_id <= 0"
                    :label="$store.state.t('IBAN')"
            ></v-text-field>
            <v-text-field
                    v-model="acc.account"
                    :disabled="acc.bank_id <= 0"
                    :label="$store.state.t('Payment Account')"
                    :counter="20"
            ></v-text-field>
          </b-tab>
        </b-tabs>
    </div>
</template>

<script>


  import qs from "qs";
  import {BanksManager} from "../../../managers/BanksManager";
  import {CurrenciesManager} from "../../../managers/CurrenciesManager";
  import {CountriesManager} from "../../../managers/CountriesManager";

  export default {
    components: {

    },

    data () {
      return {
          banks_Items: [],
          currencies_Items: [],
          countryItems: [],
      }
    },
    props: {
      pullPaymentAccounts: {type: Array, require: true, default: function () { return [];} },
    },
    created() {
        this.banksManager = new BanksManager();
        this.currenciesManager = new CurrenciesManager();
        this.countriesManager = new CountriesManager();

        this.getCurrenciesForSelect();
        this.getCountriesForSelect();

    },

    methods: {
        getCountriesForSelect: function () {
            this.countriesManager.getForSelectAccordingBanks()
                .then( (response) => {
                    if(response.data !== false){
                        this.countryItems = response.data.items;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        onCountryChange: function(acc){
            this.getBanksForSelect(acc.country_id);
            this.onBankChange(acc);
        },
        onBankChange: function (acc){
            acc.account = null;
            acc.iban = null;
            acc.currency_id = null;
        },
        getBanksForSelect: function (countryId) {
            this.banksManager.getAllByCountryId(countryId)
                .then( (response) => {
                    if(response.data !== false){
                        this.banks_Items = response.data.items;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
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

        addToPull(){

            this.pullPaymentAccounts.unshift(
                {
                    bank_id: null,
                    currency_id: null,
                    iban: null,
                    account: null,
                    country_id: null,
                }
            );

        },
    },

    computed: {

    },

    beforeDestroy () {

    },
  }
</script>
