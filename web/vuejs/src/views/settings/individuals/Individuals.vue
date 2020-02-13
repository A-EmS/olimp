<template>
  <div>
    <page-title :createProcessName="createProcessName" :heading="$store.state.t('Individuals')" :subheading="$store.state.t('Individuals actions')" icon='pe-7s-user icon-gradient bg-happy-itmeo' :starShow=false></page-title>

    <form_component :showListEventName="showListEventName" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_component>

    <b-card v-show="showList" :title="$store.state.t('Individuals')" class="main-card mb-4">
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
                    v-if="field.key !== 'actions' && field.key !== 'gender'"
                    v-model="filters[field.key]"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
            <select
                    v-if="field.key=='gender'"
                    v-model="filters['gender']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Genders')}}</option>
              <option value="1">{{$store.state.t('Male')}}</option>
              <option value="0">{{$store.state.t('Female')}}</option>
            </select>
          </td>
        </template>



        <template slot="gender" slot-scope="row">
          {{genderIntToString(row.item.gender)}}
        </template>

        <template slot="birthday" slot-scope="row">
          {{row.item.birthday | dateFormat}}
        </template>

        <template slot="create_date" slot-scope="row">
          {{row.item.create_date | dateFormat}}
        </template>

        <template slot="update_date" slot-scope="row">
          {{row.item.update_date | dateFormat}}
        </template>


        <template slot="actions" slot-scope="row">
          <table>
            <tr>
              <td><i class='lnr-pencil' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRow(parseInt(row.item.id))"> </i></td>
              <td><i class='lnr-trash' size="sm" style="cursor: pointer; font-size: large; color: red" @click.stop="" @click="confirmDeleteRow(parseInt(row.item.id), row.item.full_name)"> </i></td>
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
  var moment = require('moment');

  import qs from "qs";
  import axios from "axios";

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      form_component,
      moment,
    },
    data: () => ({
      showList: true,
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,

      showListEventName: 'showList:individual',
      updateItemListEventName: 'updateList:individual',
      createProcessName: 'create:individual',
      updateProcessName: 'update:individual',
      confirmatorInputProcessName: 'confirm:deleteIndividual',
      confirmatorOutputProcessName: 'confirmed:deleteIndividual',

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
        third_name: '',
        name: '',
        second_name: '',
        full_name: '',
        gender: '',
        birthday: '',
        inn: '',
        notice: '',

        user_name_create: '',
        create_date: '',
        user_name_update: '',
        update_date: '',
      },

      items: [],
    }),

    created: function() {

      this.getIndividuals();

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.deleteRow(data.id);
      });

      this.$eventHub.$on(this.updateItemListEventName, (data) => {
        this.getIndividuals();
      });

      this.$eventHub.$on(this.showListEventName, (data) => {
        this.showList = true;
      });

      this.setDefaultInterfaceData();
    },

    methods: {
      getIndividuals: function () {
        axios.get(window.apiDomainUrl+'/individuals/get-all', qs.stringify({}))
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
        this.showList = false;
      },

      confirmDeleteRow: function(id, name){
        this.$eventHub.$emit(this.confirmatorInputProcessName, {
          titleString: this.$store.state.t('Deleting') + '...',
          confirmString: this.$store.state.t('Confirm delete') +  ' ' + this.$store.state.t('Individual') +'..'+name,
          idToConfirm: id
        });
      },

      deleteRow: function(id){
        this.showCustomLoaderDialog = true;
        this.customDialogfrontString= this.$store.state.t('Deleting') + '...'+id;

        axios.post(window.apiDomainUrl+'/individuals/delete', qs.stringify({id:id}))
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
          { key: 'third_name', label: this.$store.state.t('Third Name'), sortable: true},
          { key: 'name', label: this.$store.state.t('Name'), sortable: true},
          { key: 'second_name', label: this.$store.state.t('Second Name'), sortable: true},
          { key: 'full_name', label: this.$store.state.t('Full Name'), sortable: true},
          { key: 'gender', label: this.$store.state.t('Gender'), sortable: true},
          { key: 'birthday', label: this.$store.state.t('Birthday'), sortable: true},
          { key: 'inn', label: this.$store.state.t('INN'), sortable: true},
          { key: 'notice', label: this.$store.state.t('Notice'), sortable: true},


          { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
          { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
          { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
          { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
        ]
      },

      genderIntToString: function (date) {
        if (date === 'undefined' || date === null){
          return ''
        } else if (parseInt(date) === 1){
          return this.$store.state.t('Male');
        } else {
          return this.$store.state.t('Female');
        }
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.confirmatorOutputProcessName);
      this.$eventHub.$off(this.updateItemListEventName);
      this.$eventHub.$off(this.showListEventName);
    },

    filters: {
      dateFormat: function (date) {
        if (date === 'undefined' || date === null){
          return ''
        }

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
