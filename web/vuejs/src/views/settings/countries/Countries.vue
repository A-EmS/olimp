<template>
  <div>
    <page-title :button-action-hide="getACL().create !== true" :createProcessName="createProcessName" :heading="$store.state.t('Countries')" :subheading="$store.state.t('Countries actions')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <form_component v-if="getACL().update === true" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_component>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Countries')" class="main-card mb-4">
      <b-row class="mb-3">
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>
        <b-col md="6" class="my-1" style="text-align: right; color: grey">
          {{paginationHeader()}}
        </b-col>
      </b-row>
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

               :items="filtered"
               :fields="fields">

        <template slot="top-row" slot-scope="{ fields }">
          <td v-for="field in fields" :key="field.key">
            <input
                    v-if="field.key !== 'actions' && field.key !== 'flag_code' && field.key !== 'world_part'"
                    v-model="filters[field.key]"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >


            <select
                    v-if="field.key=='world_part'"
                    v-model="filters['world_part']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All World Parts')}}</option>
              <option v-for="worldPartForFilter in worldPartsForFilter" :value="worldPartForFilter.name">{{worldPartForFilter.name}}</option>
            </select>
          </td>
        </template>

        <template slot="create_date" slot-scope="row">
          {{row.item.create_date | dateFormat}}
        </template>

        <template slot="update_date" slot-scope="row">
          {{row.item.update_date | dateFormat}}
        </template>

        <template slot="flag_code" slot-scope="row">
          <flag :country-acronym="row.item.flag_code"></flag>
        </template>

        <template slot="actions" slot-scope="row">
          <table>
            <tr>
              <td v-if="getACL().update === true"><i class='lnr-pencil' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRow(parseInt(row.item.id))"> </i></td>
              <td v-if="getACL().delete === true"><i class='lnr-trash' size="sm" style="cursor: pointer; font-size: large; color: red" @click.stop="" @click="confirmDeleteRow(parseInt(row.item.id), row.item.name)"> </i></td>
            </tr>
          </table>
        </template>
      </b-table>

      <b-row>
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>
      </b-row>
    </b-card>
    <v-alert
            v-if="!this.loadingProcess && getACL().list !== true"
            :value="true"
            color="error"
            icon="warning"
            outline
    >
      {{$store.state.t("You don't have permissions for it")}}
    </v-alert>
    <loadercustom :showDialog="this.loadingProcess" frontString="Permission checking..."></loadercustom>


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
  import flag from "../../components/flag";
  import {WorldPartsManager} from "../../../managers/WorldPartsManager";
  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";
  import accessMixin from "../../../mixins/accessMixin";

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      form_component,
      moment,
      flag,
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'countries',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,

      updateItemListEventName: 'updateList:countries',
      createProcessName: 'create:country',
      updateProcessName: 'update:country',
      confirmatorInputProcessName: 'confirm:deleteCountry',
      confirmatorOutputProcessName: 'confirmed:deleteCountry',

      totalRows: 0,
      perPage: 50,
      currentPage: 1,
      sortBy: null,
      sortDesc: false,
      sortDirection: 'asc',
      filter: null,

      fields: [],

      filters: {
        id: '',
        name: '',
        full_name: '',
        phone_code: '',
        phone_mask: '',
        alpha2: '',
        alpha3: '',
        iso: '',
        world_part: '',
        location: '',

        user_name_create: '',
        create_date: '',
        user_name_update: '',
        update_date: '',
      },

      worldPartsForFilter:[],
      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);
      this.worldPartsManager = new WorldPartsManager();
      this.getCountries();
      this.getWorldPartsForSelectFilter();

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.deleteRow(data.id);
      });

      this.$eventHub.$on(this.updateItemListEventName, (data) => {
        this.getCountries();
      });

      this.setDefaultInterfaceData();
    },

    methods: {
      getWorldPartsForSelectFilter: function () {
        this.worldPartsManager.getForSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.worldPartsForFilter = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      getCountries: function () {
        axios.get(window.apiDomainUrl+'/countries/get-all', qs.stringify({}))
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
        window.scrollToTop();
        this.$eventHub.$emit(this.updateProcessName, {id: id});
      },

      confirmDeleteRow: function(id, name){
        this.$eventHub.$emit(this.confirmatorInputProcessName, {
          titleString: this.$store.state.t('Deleting') + '...',
          confirmString: this.$store.state.t('Confirm delete') +  ' ' + this.$store.state.t('Countries') +'..'+name,
          idToConfirm: id
        });
      },

      deleteRow: function(id){
        this.showCustomLoaderDialog = true;
        this.customDialogfrontString= this.$store.state.t('Deleting') + '...'+id;

        axios.post(window.apiDomainUrl+'/countries/delete', qs.stringify({id:id}))
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
                      this.customDialogfrontString = this.$store.state.t('Removal did not happen, error! A link to another catalog may be present.');
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, 5000);
                    }
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      getFilterModelValue(key){
        return this.filters[key];
      },
      paginationHeader(){
        var from = (this.perPage * this.currentPage) - this.perPage + 1;
        var to = (this.perPage * this.currentPage);

        if(this.totalRows < this.perPage){
          return '1-'+ this.totalRows +' '+this.$store.state.t('of')+' ' + this.totalRows;
        } else {
          return from +'-'+ to +' '+this.$store.state.t('of')+' ' + this.totalRows;
        }
      },

      setDefaultInterfaceData: function () {
        this.customDialogfrontString = this.$store.state.t('Please stand by');
        this.fields = [
          { key: 'id', sortable: true},
          { key: 'actions', label: this.$store.state.t('Actions')},
          { key: 'name', label: this.$store.state.t('Country'), sortable: true},
          { key: 'full_name', label: this.$store.state.t('Full Country Name'), sortable: true},
          { key: 'phone_code', label: this.$store.state.t('International Phone Code'), sortable: true},
          { key: 'phone_mask', label: this.$store.state.t('Phone Mask'), sortable: true},
          { key: 'alpha2', sortable: true},
          { key: 'alpha3', sortable: true},
          { key: 'iso', sortable: true},
          { key: 'world_part', label: this.$store.state.t('World Part'), sortable: true},
          { key: 'flag_code', label: this.$store.state.t('Flag'), sortable: true},
          { key: 'location', label: this.$store.state.t('Location'), sortable: false},

          { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
          { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
          { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
          { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
        ]
      }
    },

    beforeDestroy () {
      this.$eventHub.$off(this.confirmatorOutputProcessName);
      this.$eventHub.$off(this.updateItemListEventName);
    },

    filters: {
      dateFormat: function (date) {
        if (date === 'undefined' || date === null){
          return ''
        }

        // var dateTimeArray = date.split(' ');
        // var dateArray = dateTimeArray[0].split('-');
        // date = dateArray[2] +'-'+ dateArray[1] +'-'+ dateArray[0];

        return moment(String(date)).format('YYYY-MM-DD')
      },
    },
    computed: {
      filtered () {
        const filtered = this.items.filter(item => {
          return Object.keys(this.filters).every(key =>
                  String(item[key]).toLowerCase().includes(this.getFilterModelValue(key).toString().toLowerCase())
          )
        });

        this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    }
  }
</script>
