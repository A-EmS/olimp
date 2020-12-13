<template>
  <div>
    <demo-card>
      <v-form
          ref="form"
          lazy-validation
      >
        <v-autocomplete
            v-model="interfaceLanguage"
            :items="[{'key': 'interface_language', 'value': 'ru', 'label': 'RU' }]"
            item-value="value"
            item-text="label"
            required
            :label="$store.state.t('Interface Language')"
            disabled
        ></v-autocomplete>

        <v-autocomplete
            v-model="defaultCountry"
            :items="countryItems"
            item-value="id"
            item-text="name"
            :label="$store.state.t('Country')"
            required
            @change="changeSetting('default_country', defaultCountry)"
        ></v-autocomplete>

        <v-autocomplete
            v-model="defaultOwnCompany"
            :items="ownCompanyItems"
            item-value="id"
            item-text="company"
            :label="$store.state.t('Own Company')"
            required
            @change="changeSetting('default_own_company', defaultOwnCompany)"
        ></v-autocomplete>

      </v-form>
    </demo-card>
    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import loadercustom from "../../components/loadercustom";
  import {UserSettingsManager} from "@/managers/UserSettingsManager";
  import {CountriesManager} from "@/managers/CountriesManager";
  import {UserOwnCompaniesManager} from "@/managers/UserOwnCompaniesManager";
  import DemoCard from "@/Layout/Components/DemoCard";

  export default {
    components: {
      loadercustom,
      'demo-card': DemoCard,
    },
    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',

        interfaceLanguage: null,
        defaultCountry: null,
        defaultOwnCompany: null,

        countryItems: [],
        ownCompanyItems: [],
      }
    },
    props: {
      userId: {type: Number, require: true},
    },
    mounted() {
      this.userSettingsManager = new UserSettingsManager();
      this.countriesManager = new CountriesManager();
      this.userOwnCompaniesManager = new UserOwnCompaniesManager();

      this.getUserSettings();
      this.getCountriesForSelect();
      this.getUserOwnCompanies();

      this.$eventHub.$on('user_settings:refresOwnCompanies', () => {
        this.getUserOwnCompanies();
      });
    },
    methods: {
      getUserSettings: function () {
        this.userSettingsManager.getAllByUserId(this.userId)
            .then( (response) => {
              if(response.data !== false){
                let s = {};

                response.data.items.forEach((item) => {
                  s[item.key] = item.value;
                });

                this.interfaceLanguage = typeof s['interface_language'] !== 'undefined' ? s['interface_language'] : null;
                this.defaultCountry = typeof s['default_country'] !== 'undefined' ? s['default_country'] : null;
                this.defaultOwnCompany = typeof s['default_own_company'] !== 'undefined' ? s['default_own_company'] : null;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      changeSetting: function (settingName, settingValue) {
        this.showCustomLoaderDialog = true;
        this.userSettingsManager.change({'key': settingName, 'value': settingValue, 'user_id': this.userId})
        .then(() => {
          this.showCustomLoaderDialog = false;
        });
      },
      getUserOwnCompanies: function (){
        this.userOwnCompaniesManager.getAllByUserId(this.userId)
            .then((response) => {
              this.ownCompanyItems = response.data.items;
            })
      },
      getCountriesForSelect: function () {
        this.countriesManager.getAll()
            .then( (response) => {
              if(response.data !== false){
                this.countryItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
    },

    beforeDestroy () {
      this.$eventHub.$off('user_settings:refresOwnCompanies');
    },
  }
</script>
