<template>
  <div>
    <div style="margin: 0 0 -40px 4px">
      <page-title :hideTitleHeading="(parseInt(contractor_id) > 0)" :createBtnOnLeft="(parseInt(contractor_id) > 0)" :button-action-hide="getACL().create !== true" :createProcessName="createProcessName" :heading="$store.state.t('Finance Documents')" :subheading="$store.state.t('Finance Documents actions')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>
    </div>

    <form_component :contractorInputId="contractor_id" v-if="getACL().update === true" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_component>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Finance Documents')" class="main-card mb-4">
      <b-row class="mb-3">
        <b-col md="6" class="my-1">
          <b-pagination v-on:change="getByPage()" :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
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

               :sort-by.sync="sortBy"
               :sort-desc.sync="sortDesc"
               :sort-direction="sortDirection"

               :items="items"
               :fields="fields">

        <template slot="top-row" slot-scope="{ fields }">
          <td v-for="field in fields" :key="field.key">
            <input
                    v-if="field.key !== 'actions' && field.key !== 'country' && field.key !== 'document_type'
                    && field.key !== 'document_status' && field.key !== 'own_company'"
                    v-model="filters[field.key]"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
                    v-on:change="getByFilter()"
            >

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='document_status'"
                    v-model="filters['document_status_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Document Statuses')}}</option>
              <option v-for="item in documentStatusItems" :value="item.id">{{item.name}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='own_company'"
                    v-model="filters['own_company_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Own Companies')}}</option>
              <option v-for="item in ownCompanyItems" :value="item.id">{{item.company}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='country'"
                    v-model="filters['country_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Countries')}}</option>
              <option v-for="countryForFilter in countriesForFilter" :value="countryForFilter.id">{{countryForFilter.name}}</option>
            </select>

            <select
                    v-on:change="getByFilter()"
                    v-if="field.key=='document_type'"
                    v-model="filters['document_type_id']"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
              <option value="">{{$store.state.t('All Document Types')}}</option>
              <option v-for="documentType in documentTypeItems" :value="documentType.id">{{documentType.name}}</option>
            </select>

          </td>
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
              <td v-if="getACL().update === true"><i class='lnr-pencil' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRow(parseInt(row.item.id))"> </i></td>
              <td v-if="getACL().delete === true"><i class='lnr-trash' size="sm" style="cursor: pointer; font-size: large; color: red" @click.stop="" @click="confirmDeleteRow(parseInt(row.item.id), row.item.id)"> </i></td>
            </tr>
          </table>
        </template>
      </b-table>

      <b-row>
        <b-col md="6" class="my-1">
          <b-pagination v-on:change="getByPage()" :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
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

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import loadercustom from "../../../components/loadercustom";
  import confirmator from "../../../components/confirmator";
  import form_component from "./form_component";
  var moment = require('moment');

  import accessMixin from "../../../../mixins/accessMixin";
  import {OwnCompaniesManager} from "../../../../managers/OwnCompaniesManager";
  import {DocumentStatusesManager} from "../../../../managers/DocumentsStatusesManager";
  import {FinanceDocumentsManager} from "../../../../managers/FinanceDocumentsManager";
  import {CountriesManager} from "../../../../managers/CountriesManager";
  import {DocumentTypesManager} from "../../../../managers/DocumentTypesManager";
  import timerdown from "../../../components/timerdown";

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      form_component,
      timerdown,
      moment,
    },

    mixins: [accessMixin],

    props: {
      contractor_id: {type: Number, require: false, default: 0},
    },

    data: () => ({
      accessLabelId: 'financeDocuments',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,

      updateItemListEventName: 'updateList:financeDocuments',
      createProcessName: 'create:financeDocument',
      updateProcessName: 'update:financeDocument',
      confirmatorInputProcessName: 'confirm:deleteFinanceDocument',
      confirmatorOutputProcessName: 'confirmed:deleteFinanceDocument',

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
        document_code: '',
        parent_document: '',
        date: '',
        contractor: '',
        country_id: '',
        document_type_id: '',
        own_company_id: '',
        document_status_id: '',
        notice: '',

        // summ: '',
        // template: '',
        // signed_document_scan: '',

        user_name_create: '',
        create_date: '',
        user_name_update: '',
        update_date: '',
      },

      countriesForFilter: [],
      documentTypeItems: [],
      ownCompanyItems: [],
      documentStatusItems: [],

      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);

      this.documentStatusManager = new DocumentStatusesManager();
      this.documentTypeManager = new DocumentTypesManager();
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.countriesManager = new CountriesManager();
      this.financeDocumentsManager = new FinanceDocumentsManager();

      this.getDocumentsStatuses();
      this.getDocumentTypes();
      this.getOwnCompanies();
      this.getCountriesForSelectFilter();
      this.getFinanceDocuments();

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.deleteRow(data.id);
      });

      this.$eventHub.$on(this.updateItemListEventName, (data) => {
        this.getFinanceDocuments();
      });

      this.setDefaultInterfaceData();
    },

    methods: {
      getCountriesForSelectFilter: function () {
        this.countriesManager.getAllForSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.countriesForFilter = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getDocumentTypes: function () {
        this.documentTypeManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.documentTypeItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getFinanceDocuments: function () {
        var emptyFilter = {};
        if (this.contractor_id > 0) {
          emptyFilter['contractor_id'] = this.contractor_id;
        }
        this.financeDocumentsManager.getByPage(this.currentPage, this.perPage, emptyFilter)
          .then( (response) => {
            if(response.data !== false){
              this.items = response.data.items;
              this.totalRows = response.data.count;
            }
          })
          .catch(function (error) {
            console.log(error);
          });
      },

      getDocumentsStatuses: function() {
        this.documentStatusManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.documentStatusItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getOwnCompanies: function () {
        this.ownCompaniesManager.getAll()
                .then( (response) => {
                  if(response.data !== false){
                    this.ownCompanyItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      getByPage: function () {
        this.$nextTick(() => {
          this.showCustomLoaderDialog = true;
          if (this.contractor_id > 0) {
            this.filters['contractor_id'] = this.contractor_id;
          }
          this.financeDocumentsManager.getByPage(this.currentPage, this.perPage, this.filters)
                  .then( (response) => {
                    if(response.data !== false){
                      this.items = response.data.items;
                      this.showCustomLoaderDialog = false;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      },

      getByFilter: function () {
        this.$nextTick(() => {
          this.showCustomLoaderDialog = true;
          if (this.contractor_id > 0) {
            this.filters['contractor_id'] = this.contractor_id;
          }
          this.financeDocumentsManager.getByPage(1, this.perPage, this.filters)
                  .then( (response) => {
                    if(response.data !== false){
                      this.items = response.data.items;
                      this.totalRows = response.data.count;
                      this.currentPage = 1;
                      this.showCustomLoaderDialog = false;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      },

      updateRow: function(id){
        window.scrollToTop();
        this.$eventHub.$emit(this.updateProcessName, {id: id});
      },

      confirmDeleteRow: function(id, name){
        this.$eventHub.$emit(this.confirmatorInputProcessName, {
          titleString: this.$store.state.t('Deleting') + '...',
          confirmString: this.$store.state.t('Confirm delete') +  ' ' + this.$store.state.t('Finance Documents') +'..'+name,
          idToConfirm: id
        });
      },

      deleteRow: function(id){
        this.showCustomLoaderDialog = true;
        this.customDialogfrontString= this.$store.state.t('Deleting') + '...'+id;
        this.financeDocumentsManager.delete({id:id})
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

                      this.customDialogfrontString = response.data.error;
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                        this.customDialogfrontString = this.$store.state.t('Please stand by');
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
          { key: 'document_type', label: this.$store.state.t('Document Type'), sortable: true},
          { key: 'document_code', label: this.$store.state.t('Document Code'), sortable: true},
          { key: 'parent_document', label: this.$store.state.t('Parent Document'), sortable: true},
          { key: 'date', label: this.$store.state.t('Date'), sortable: true},
          { key: 'contractor', label: this.$store.state.t('Contractor'), sortable: true},
          { key: 'country', label: this.$store.state.t('Country'), sortable: true},
          { key: 'own_company', label: this.$store.state.t('Own Company'), sortable: true},
          { key: 'document_status', label: this.$store.state.t('Document Status'), sortable: true},
          { key: 'notice', label: this.$store.state.t('Notice'), sortable: true},
          // { key: 'summ', label: this.$store.state.t('Sum'), sortable: true},
          // { key: 'template', label: this.$store.state.t('Template'), sortable: true},
          // { key: 'signed_document_scan', label: this.$store.state.t('Signed Document Scan'), sortable: true},

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

        // this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    }
  }
</script>
