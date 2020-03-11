<template>
    <div>
        <b-button class="mr-2 mb-2 btn-pill" variant="success" @click="addToPull()">{{$store.state.t('Add Payment Account')}}</b-button>
        <br />

        <b-tabs card>
          <b-tab :title="$store.state.t('Payment Account')" v-for="acc in pullPaymentAccounts">
            <v-autocomplete
                    v-model="acc.bank_id"
                    :items="banks_Items"
                    item-value="id"
                    item-text="bank_name"
                    :label="$store.state.t('Bank')"
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
                    :label="$store.state.t('IBAN')"
            ></v-text-field>
            <v-text-field
                    v-model="acc.account"
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

  export default {
    components: {

    },

    data () {
      return {
          banks_Items: [],
          currencies_Items: [],
      }
    },
    props: {
      pullPaymentAccounts: {type: Array, require: true, default: function () { return [];} },
    },
    created() {
        this.banksManager = new BanksManager();
        this.currenciesManager = new CurrenciesManager();
        this.getBanksForSelect();
        this.getCurrenciesForSelect();

    },

    methods: {
        onBankChange: function (acc){
            acc.account = null;
            acc.iban = null;
            acc.currency_id = null;
        },
        getBanksForSelect: function () {
            this.banksManager.getAll()
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
                    account: null
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
