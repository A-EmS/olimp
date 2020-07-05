<template>
  <div>
    <page-title :button-action-hide="this.getACL().create !== true" :createProcessName="createProcessName" :heading="$store.state.t('Addresses')" :subheading="$store.state.t('Addresses actions')" icon='pe-7s-id icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <table_list_component v-if="this.getACL().list === true"></table_list_component>
    <v-alert
            v-else
            :value="true"
            color="error"
            icon="warning"
            outline
    >
      {{$store.state.t("You don't have permissions for it")}}
    </v-alert>

  </div>
</template>

<script>

  import PageTitle from "../../../Layout/Components/PageTitle.vue";
  import table_list_component from "./table_list_component";

  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";
  import accessMixin from "../../../mixins/accessMixin";

  export default {
    components: {
      PageTitle,
      table_list_component,

    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'addresses',
      createProcessName: 'create:address',
    }),

    created: function() {
      this.loadACL(this.accessLabelId);
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
