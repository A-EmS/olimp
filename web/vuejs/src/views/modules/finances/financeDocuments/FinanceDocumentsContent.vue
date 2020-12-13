<template>
  <div>
    <div style="margin: 0 0 -40px 4px">
      <page-title :hideTitleHeading="(parseInt(document_id) > 0)" :createBtnOnLeft="(parseInt(document_id) > 0)" :button-action-hide="getACL().create !== true" :createProcessName="createProcessName" :heading="$store.state.t('Finance Documents Content')" icon='pe-7s-global icon-gradient bg-happy-itmeo' :starShow=false></page-title>
    </div>

    <form_content_contract :document_id="document_id" v-if="getACL().update === true && current_document_type_scenario == constants.documentScenarioIdContract" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_content_contract>
    <form_content_annex :document_id="document_id" v-if="getACL().update === true && current_document_type_scenario == constants.documentScenarioIdAnnex" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_content_annex>
    <form_content_account :document_id="document_id" v-if="getACL().update === true && current_document_type_scenario == constants.documentScenarioIdAccount" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_content_account>
    <form_content_act :document_id="document_id" v-if="getACL().update === true && current_document_type_scenario ==  constants.documentScenarioIdAct" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_content_act>

    <b-card v-if="getACL().list === true" :title="$store.state.t('Finance Documents Content')" class="main-card mb-4">
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
                    v-if="field.key !== 'actions'"
                    v-model="filters[field.key]"
                    style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                    class="col-md-12"
            >
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
              <td v-if="getACL().update === true && current_document_type_scenario !== constants.documentScenarioIdAccount"><i class='lnr-pencil' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRow(parseInt(row.item.id))"> </i></td>
              <td v-if="getACL().delete === true"><i class='lnr-trash' size="sm" style="cursor: pointer; font-size: large; color: red" @click.stop="" @click="confirmDeleteRow(parseInt(row.item.id), row.item.id)"> </i></td>
            </tr>
          </table>
        </template>
        <template slot="parent_content_id" slot-scope="row">
          <i v-if="getACL().update === true" class='' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRow(parseInt(row.item.parent_content_id))"><u>{{row.item.parent_content_id}}</u></i>
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

  import PageTitle from "../../../../Layout/Components/PageTitle.vue";
  import loadercustom from "../../../components/loadercustom";
  import confirmator from "../../../components/confirmator";
  import form_content_contract from "./contentFormComponents/form_content_contract";
  import form_content_annex from "./contentFormComponents/form_content_annex";
  import form_content_act from "./contentFormComponents/form_content_act";
  import form_content_account from "./contentFormComponents/form_content_account";
  var moment = require('moment');

  import accessMixin from "../../../../mixins/accessMixin";
  import {FinanceDocumentsContentManager} from "../../../../managers/FinanceDocumentsContentManager";
  import constantsMixin from "../../../../mixins/constantsMixin";

  export default {
    components: {
      PageTitle,
      loadercustom,
      confirmator,
      form_content_contract,
      form_content_annex,
      form_content_act,
      form_content_account,
      moment,
    },

    mixins: [accessMixin, constantsMixin],

    props: {
      document_id: {type: Number, require: false, default: 0},
      current_document_type_scenario: {type: Number, require: false, default: 0},
    },

    data: () => ({
      accessLabelId: 'financeDocumentsContent',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,

      updateItemListEventName: 'updateList:financeDocumentsContent',
      createProcessName: 'create:financeDocumentsContent',
      updateProcessName: 'update:financeDocumentsContent',
      confirmatorInputProcessName: 'confirm:deleteFinanceDocumentsContent',
      confirmatorOutputProcessName: 'confirmed:deleteFinanceDocumentsContent',

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
        document: '',
        parent_content_id: '',
        // percent: '',
        product: '',
        service: '',
        amount: '',
        cost_without_tax: '',
        cost_with_tax: '',
        summ_without_tax: '',
        summ_with_tax: '',
        summ_tax: '',
        notice: '',
        start_date: '',
        end_date: '',

        user_name_create: '',
        create_date: '',
        user_name_update: '',
        update_date: '',
      },

      items: [],
    }),

    created: function() {
      this.loadACL(this.accessLabelId);
      this.financeDocumentsContentManager = new FinanceDocumentsContentManager();
      this.getFinanceDocumentsContent();

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.deleteRow(data.id);
      });

      this.$eventHub.$on(this.updateItemListEventName, (data) => {
        this.getFinanceDocumentsContent();
      });

      this.setDefaultInterfaceData();
    },

    methods: {
      getFinanceDocumentsContent: function () {
        this.financeDocumentsContentManager.getAllByDocumentId(this.document_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.items = response.data.items;
                    this.totalRows = response.data.items.length;
                      this.$emit('documentContentLoaded', {'contentCount': this.totalRows});
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
          confirmString: this.$store.state.t('Confirm delete') +  ' ' + this.$store.state.t('Finance Documents Content') +'..'+name,
          idToConfirm: id
        });
      },

      deleteRow: function(id){
        this.showCustomLoaderDialog = true;
        this.customDialogfrontString= this.$store.state.t('Deleting') + '...'+id;

        this.financeDocumentsContentManager.delete(id)
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
          { key: 'document', label: this.$store.state.t('Document'), sortable: true},
          // { key: 'parent_content_id', label: this.$store.state.t('Parent Content Id'), sortable: true},
          // { key: 'percent', label: this.$store.state.t('Percent'), sortable: true},
          { key: 'product', label: this.$store.state.t('Product'), sortable: true},
          { key: 'service', label: this.$store.state.t('Service'), sortable: true},
          { key: 'amount', label: this.$store.state.t('Amount'), sortable: true},
          { key: 'cost_without_tax', label: this.$store.state.t('Cost Without Tax'), sortable: true},
          { key: 'cost_with_tax', label: this.$store.state.t('Cost With Tax'), sortable: true},
          { key: 'summ_without_tax', label: this.$store.state.t('Summ Without Tax'), sortable: true},
          { key: 'summ_with_tax', label: this.$store.state.t('Summ With Tax'), sortable: true},
          { key: 'summ_tax', label: this.$store.state.t('Summ Tax'), sortable: true},
          { key: 'notice', label: this.$store.state.t('Notice'), sortable: true},
          { key: 'start_date', label: this.$store.state.t('Start Date'), sortable: true},
          { key: 'end_date', label: this.$store.state.t('End Date'), sortable: true},

          { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
          { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
          { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
          { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
        ];

        if (this.current_document_type_scenario ==  this.constants.documentScenarioIdAct) {
          this.fields = [
            { key: 'id', sortable: true},
            { key: 'actions', label: this.$store.state.t('Actions')},
            { key: 'document', label: this.$store.state.t('Document'), sortable: true},
            { key: 'product', label: this.$store.state.t('Product'), sortable: true},
            { key: 'service', label: this.$store.state.t('Service'), sortable: true},
            { key: 'amount', label: this.$store.state.t('Amount'), sortable: true},

            { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
            { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
            { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
            { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
          ];
        }

        if (this.current_document_type_scenario ==  this.constants.documentScenarioIdAccount) {
          this.fields = [
            { key: 'id', sortable: true},
            { key: 'actions', label: this.$store.state.t('Actions')},
            { key: 'document', label: this.$store.state.t('Document'), sortable: true},
            { key: 'product', label: this.$store.state.t('Product'), sortable: true},
            { key: 'service', label: this.$store.state.t('Service'), sortable: true},
            { key: 'amount', label: this.$store.state.t('Amount'), sortable: true},

            { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
            { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
            { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
            { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
          ];
        }
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

        this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    }
  }
</script>
