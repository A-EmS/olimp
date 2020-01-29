import Vue from 'vue'
import './plugins/vuetify'
import router from './router'
import store from "@vue/cli-service/generator/vuex/template/src/store";

import BootstrapVue from "bootstrap-vue"

import App from './App'

import Default from './Layout/Wrappers/baseLayout.vue';
import Pages from './Layout/Wrappers/pagesLayout.vue';
import Apps from './Layout/Wrappers/appLayout.vue';
import {empty} from "leaflet/src/dom/DomUtil";

import {VM} from './managers/VocabularyManager.js';
import qs from "qs"; // or './module'
var VocabularyManager = new VM();

Vue.config.productionTip = false;

Vue.use(BootstrapVue);

Vue.component('default-layout', Default);
Vue.component('userpages-layout', Pages);
Vue.component('apps-layout', Apps);

new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App },
  created: function() {
    this.$store.state.t = (string) => {

        var currentInterfaceVocabulary = JSON.parse(localStorage.getItem('currentInterfaceVocabulary'));
        if (typeof this.$store.state.currentInterfaceVocabulary == 'undefined' && currentInterfaceVocabulary !== null){
            this.$store.state.currentInterfaceVocabulary = currentInterfaceVocabulary;
        }

        if (
            typeof this.$store.state.currentInterfaceVocabulary != 'undefined'
            && typeof this.$store.state.currentInterfaceVocabulary[''+string] != 'undefined'
            && this.$store.state.currentInterfaceVocabulary[''+string] !== ''
            && this.$store.state.currentInterfaceVocabulary[''+string] !== null
      ){
        return this.$store.state.currentInterfaceVocabulary[''+string];
      } else if (
          typeof this.$store.state.currentInterfaceVocabulary != 'undefined'
          && typeof this.$store.state.currentInterfaceVocabulary[''+string] == 'undefined'
      ) {

        VocabularyManager.create(string)
            .then( (response) => {
          if (response.data !== false){
            this.$store.state.currentInterfaceVocabulary[''+string] = '';
            this.$forceUpdate();

                this.$eventHub.$emit('updateList:vocabularies');
          }
        })
            .catch(function (error) {
              console.log(error);
            });
      }

      return string;
    }
  },
  methods: {

  }
});

Vue.prototype.$eventHub = new Vue({
  props: {
  },
  data: function () {
    return {
    }
  },
  created: function () {
  },
  beforeMount: function() {
  },
  mounted() {
  },
  computed: {
  },
  updated: function () {
  },
  methods: {
  },
  template: ``,
});