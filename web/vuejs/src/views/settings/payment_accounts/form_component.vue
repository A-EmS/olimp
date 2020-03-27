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
                  :items="countryItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Country')"
                  required
                  @change="onCountryChange"
          ></v-autocomplete>
          <v-autocomplete
                  v-model="bank_id"
                  :error-messages="bank_idErrors"
                  :items="banks_Items"
                  item-value="id"
                  item-text="bank_full_search_info"
                  :label="$store.state.t('Bank') + ' / ' + $store.state.t('IBAN') + ' / ' + $store.state.t('Payment Account')"
                  required
                  @input="$v.bank_id.$touch()"
                  @blur="$v.bank_id.$touch()"
                  @change="onBankChange"
          ></v-autocomplete>
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
          <v-text-field
                  v-model="iban"
                  :disabled="bank_id <= 0"
                  :error-messages="accountIbanErrors"
                  :label="$store.state.t('IBAN')"
          ></v-text-field>
          <v-text-field
                  v-model="account"
                  :disabled="bank_id <= 0"
                  :error-messages="accountIbanErrors"
                  :label="$store.state.t('Payment Account')"
                  :counter="20"
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

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';
  import {CM} from "../../../managers/ContractorsManager";
  import loadercustom from "../../components/loadercustom";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";
  import {PaymentAccountsManager} from "../../../managers/PaymentAccountsManager";
  import {BanksManager} from "../../../managers/BanksManager";
  import {CurrenciesManager} from "../../../managers/CurrenciesManager";
  import {CountriesManager} from "../../../managers/CountriesManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
      CM
    },

    mixins: [validationMixin],

    validations: {
      bank_id: { required },
      contractor_id: { required },
      currency_id: { required },
    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',
        confirmDeleteString: '',
        showConfirmatorDialog: false,

        contractorManager:null,
        contractorShow: true,
        showDialog: false,
        valid: true,
        header: '',

        rowId: 0,
        contractor_id: null,
        account: null,
        iban: null,
        banks_Items: [],
        bank_id: null,
        currencies_Items: [],
        currency_id: null,
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
      this.contractorManager = new CM();
      this.paymentAccountsManager = new PaymentAccountsManager();
      this.banksManager = new BanksManager();
      this.currenciesManager = new CurrenciesManager();
      this.countriesManager = new CountriesManager();

      this.getCountriesForSelect();
      this.getCurrenciesForSelect();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;

        if (typeof data.refId !== 'undefined' && data.refId > 0){
          this.getContractorByRefIdAndType({ref_id:data.refId, is_entity: data.isEntity})
        }

      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.paymentAccountsManager.get(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.account = response.data.account;
                    this.iban = response.data.iban;
                    this.bank_id = response.data.bank_id;
                    this.contractor_id = response.data.contractor_id;
                    this.currency_id = response.data.currency_id;
                    this.country_id = response.data.country_id;

                    this.getBanksForSelect(this.country_id);

                    if (data.notOriginalPage === true) {
                      this.contractorShow = false;
                    }
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
      onCountryChange: function(){
        this.bank_id = null;
        this.getBanksForSelect(this.country_id);
        this.onBankChange();
      },
      onBankChange: function (){
                this.account = null;
                this.iban = null;
                this.currency_id = null;
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
      getContractorByRefIdAndType: function (data) {
        this.contractorManager.getContractorByRefIdAndType(data)
                .then( (response) => {
                  if(response.data !== false){
                    this.contractor_id = response.data.item.id;
                    this.contractorShow = false;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      submit: function () {
        this.$v.$touch();
        if (!this.$v.$invalid && ((this.iban !== null && this.iban.length > 0) || (this.account !== null && this.account.length > 0))) {
          if (this.rowId === 0){
            this.create();
          } else {
            this.update();
          }
        }
      },
      create: function(){

        var createData = {
          iban: this.iban,
          account: this.account,
          contractor_id: this.contractor_id,
          bank_id: this.bank_id,
          currency_id: this.currency_id
        };

        this.paymentAccountsManager.create(createData)
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
          iban: this.iban,
          account: this.account,
          contractor_id: this.contractor_id,
          bank_id: this.bank_id,
          currency_id: this.currency_id,
          id: this.rowId
        };

        this.paymentAccountsManager.update(updateData)
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
        if (typeof this.$parent.showAdditionalCreatingButton !== 'undefined'){
          this.$parent.showAdditionalCreatingButton = true;
        }
      },

      setDefaultData () {
        this.account = null;
        this.iban = null;
        this.contractor_id = null;
        this.bank_id = null;
        this.currency_id = null;
        this.rowId = 0;
      }
    },

    computed: {
      accountIbanErrors () {
        const errors = [];
        if (
                (this.account === null || this.account === '') && (this.iban === null || this.iban === '')
        ){
          errors.push(this.$store.state.t('IBAN or Account must be filled'))
        }
        return errors
      },
      currency_idErrors () {
        const errors = [];
        if (!this.$v.currency_id.$dirty) return errors;
        !this.$v.currency_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      bank_idErrors () {
        const errors = [];
        if (!this.$v.bank_id.$dirty) return errors;
        !this.$v.bank_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
