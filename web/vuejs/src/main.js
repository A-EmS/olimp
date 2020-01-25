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

      if (
            typeof this.$store.state.currentInterfaceVocabulary != 'undefined'
            && typeof this.$store.state.currentInterfaceVocabulary[''+string] != 'undefined'
      ){
        return this.$store.state.currentInterfaceVocabulary[''+string];
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