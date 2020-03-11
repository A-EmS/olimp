<template>
  <div>
    <page-title :createProcessName="createProcessName" :heading="$store.state.t('Payment Accounts')" :subheading="$store.state.t('Payment Accounts actions')" icon='pe-7s-id icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <table_list_component></table_list_component>

  </div>
</template>

<script>

  import PageTitle from "../../../Layout/Components/PageTitle.vue";
  import table_list_component from "./table_list_component";

  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";

  export default {
    components: {
      PageTitle,
      table_list_component,

    },
    data: () => ({
      createProcessName: 'create:paymentAccounts',
    }),

    created: function() {

    },

    methods: {

    },

    beforeDestroy () {

    },

    filters: {

    },
    computed: {

    }
  }
</script>
