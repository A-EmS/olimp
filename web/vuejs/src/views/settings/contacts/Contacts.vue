<template>
  <div>
    <page-title :createProcessName="createProcessName" :heading="$store.state.t('Contacts')" :subheading="$store.state.t('Contacts actions')" icon='pe-7s-id icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <table_list_component></table_list_component>

  </div>
</template>

<script>

  import PageTitle from "../../../Layout/Components/PageTitle.vue";
  import table_list_component from "./table_list_component";

  export default {
    components: {
      PageTitle,
      table_list_component
    },
    data: () => ({
      createProcessName: 'create:contact',
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
