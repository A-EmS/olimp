<template>
  <div>
    <page-title :createProcessName="createProcessName" heading='World Parts' subheading='World Parts actions' icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <form_component :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_component>

    <b-card title="World Parts" class="main-card mb-4">
      <b-table :striped="true"
               :bordered="true"
               :outlined="true"
               :small="true"
               :hover="false"
               :dark="false"
               :fixed="false"
               :foot-clone="true"

               :current-page="currentPage"
               :per-page="perPage"
               :filter="filter"
               :sort-by.sync="sortBy"
               :sort-desc.sync="sortDesc"
               :sort-direction="sortDirection"
               @filtered="onFiltered"

               :items="items"
               :fields="fields">

        <template slot="user_name_create" slot-scope="row">
          <a :href="getUserLink(parseInt(row.item.user_name_create_id))" @click="goToUrl(parseInt(row.item.user_name_create_id))"> {{row.item.user_name_create}}</a>
        </template>

        <template slot="user_name_update" slot-scope="row">
          <a :href="getUserLink(parseInt(row.item.user_name_update_id))" @click="goToUrl(parseInt(row.item.user_name_update_id))">{{row.item.user_name_update}}</a>
        </template>

        <template slot="actions" slot-scope="row">
          <b-button size="sm" @click.stop="" @click="updateRow(parseInt(row.item.id))" variant="secondary">Update</b-button>
          &nbsp;
          <b-button size="sm" @click.stop="" @click="confirmDeleteRow(parseInt(row.item.id), row.item.name)" variant="danger">Delete</b-button>
        </template>
      </b-table>

      <b-row>
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>
      </b-row>
    </b-card>



    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
    <confirmator
            :handlerInputProcessName="confirmatorInputProcessName"
            :handlerOutputProcessName="confirmatorOutputProcessName">
    </confirmator>
  </div>
</template>

<script>

  import PageTitle from "../../../Layout/Components/PageTitle.vue";
  import loadercustom from "../../components/loadercustom";
  import confirmator from "../../components/confirmator";
  import form_component from "./form_component";

  import qs from "qs";
  import axios from "axios";

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      form_component,
    },
    data: () => ({
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,

      updateItemListEventName: 'updateList:worldPart',
      createProcessName: 'create:worldPart',
      updateProcessName: 'update:worldPart',
      confirmatorInputProcessName: 'confirm:deleteWorldPart',
      confirmatorOutputProcessName: 'confirmed:deleteWorldPart',

      totalRows: 0,
      perPage: 25,
      currentPage: 1,
      sortBy: null,
      sortDesc: false,
      sortDirection: 'asc',
      filter: null,

      fields: [
        { key: 'id', sortable: true},
        { key: 'name', sortable: true},
        { key: 'user_name_create', sortable: true},
        { key: 'create_date', sortable: true},
        { key: 'user_name_update', sortable: true},
        { key: 'update_date', sortable: true},
        { key: 'actions'},
      ],

      items: [],
    }),

    created: function() {

      this.getWorldParts();

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.deleteRow(data.id);
      });

      this.$eventHub.$on(this.updateItemListEventName, (data) => {
        this.getWorldParts();
      });

    },

    methods: {
      getWorldParts: function () {
        axios.get(window.apiDomainUrl+'/world-parts/get-all-parts', qs.stringify({}))
          .then( (response) => {
            if(response.data !== false){
              this.items = response.data.items;
              this.totalRows = response.data.items.length;
            }
          })
          .catch(function (error) {
            console.log(error);
          });
      },

      updateRow: function(id){
        this.$eventHub.$emit(this.updateProcessName, {id: id});
      },

      confirmDeleteRow: function(id, name){
        this.$eventHub.$emit(this.confirmatorInputProcessName, {
          titleString: 'Deleting...',
          confirmString: 'Confirm delete World Part..'+name,
          idToConfirm: id
        });
      },

      deleteRow: function(id){
        this.showCustomLoaderDialog = true;
        this.customDialogfrontString='Deleting...'+id;

        axios.post(window.apiDomainUrl+'/world-parts/delete', qs.stringify({id:id}))
                .then( (response) => {
                  if(response.data !== false){
                    if(response.data.status === true){

                      var currentIndex = this.items.indexOf(this.items.find(obj => obj.id == id));

                      delete(this.items[currentIndex]);
                      this.items = this.items.filter(function (el) {
                        return el != '';
                      });

                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, window.config.time_popup);
                    } else {
                      this.customDialogfrontString='Error...!!!!!!!!';
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, 3000);
                    }
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      getUserLink(userId){
        return window.apiDomainUrl+'/#/user/'+userId;
      },
      goToUrl(userId){
        this.$router.push({ name: 'user', params:  {id:userId} });
      },
      onFiltered (filteredItems) {
        this.totalRows = filteredItems.length;
        this.currentPage = 1;
      }

    },

    beforeDestroy () {
      this.$eventHub.$off(this.confirmatorOutputProcessName);
      this.$eventHub.$off(this.updateItemListEventName);
    },
  }
</script>
