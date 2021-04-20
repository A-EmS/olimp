<template>
  <div>
    <b-card class="main-card mb-4">
      <v-btn color="success" @click="updateStagesLaborCosts">{{$store.state.t('Save')}}</v-btn>
      <b-row class="mb-3">
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>

        <b-col md="6" class="my-1" style="text-align: right; color: grey">
          {{paginationHeader()}}
        </b-col>
      </b-row>
      <b-table
          v-if="price_list_id > 0"
          :striped="true"
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
          :tbody-tr-class="rowClass"

          :items="filtered"
          :fields="fields">

        <template slot="top-row" slot-scope="{ fields }">
          <td v-for="field in fields" :key="field.key">
            <input
                v-if="field.key !== 'status'"
                v-model="filters[field.key]"
                style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                class="col-md-12"
            >
          </td>
        </template>

        <template slot="cost_for_all_days_sum" slot-scope="row">
          {{row.item.cost_for_all_days_sum}}
        </template>
        <template slot="cost_for_offer_sum" slot-scope="row">
          {{row.item.cost_for_offer_sum}}
        </template>
        <template slot="project_stage_duration_time_days" slot-scope="row">
          <input v-model="row.item.project_stage_duration_time_days" type="number">
        </template>
        <template slot="create_date" slot-scope="row">
          {{row.item.create_date | dateFormat}}
        </template>

        <template slot="update_date" slot-scope="row">
          {{row.item.update_date | dateFormat}}
        </template>

        <template slot="status" slot-scope="row">
          <table>
            <tr>
              <td v-if="parseInt(row.item.status) === 0"><i class='lnr-redo' size="sm" style="cursor: pointer; font-size: large; color: black !important;" @click="row.item.status = 1"> </i></td>
              <td v-else><i class='lnr-cross' size="sm" style="cursor: pointer; font-size: large; color: red !important;" @click="row.item.status = 0"> </i></td>
            </tr>
          </table>
        </template>
      </b-table>

      <b-row>
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>
      </b-row>
      <v-btn color="success" @click="updateStagesLaborCosts">{{$store.state.t('Save')}}</v-btn>
    </b-card>


    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<style>
.table-deleted-row {
  color: lightgray;
}
.apply-to-all-extra-charge {
  color: lightgray;
}
.apply-to-all-extra-charge:hover {
  color: black;
}
</style>
<script>

import PageTitle from "../../../../Layout/Components/PageTitle.vue";
import loadercustom from "../../../components/loadercustom";
import confirmator from "../../../components/confirmator";
var moment = require('moment');

import accessMixin from "../../../../mixins/accessMixin";
import {RequestContentManager} from "@/managers/RequestContentManager";
import VueTextareaAutosize from 'vue-textarea-autosize'
import Vue from "vue";
import constantsMixin from "@/mixins/constantsMixin";
Vue.use(VueTextareaAutosize);

export default {
  components: {
    PageTitle,
    loadercustom,
    confirmator,
    moment,
  },

  mixins: [accessMixin, constantsMixin],

  props: {
    request_id: {type: Number, require: false, default: 0},
    country_id: {type: Number, require: false, default: 0},
    own_company_id: {type: Number, require: false, default: 0},
    updateCustomEventName: {type: String, require: false, default: ''},
    updateRequestProjectStageTrigger: {type: String, require: false, default: ''},
  },

  data: () => ({
    accessLabelId: 'requests',
    showPatternDialog: false,
    showCustomLoaderDialog: false,
    customDialogfrontString: 'Please stand by',
    confirmDeleteString: '',
    showConfirmatorDialog: false,
    updateItemListEventName: 'updateList:requestStagesContent',
    createProcessName: 'create:requestStagesContent',
    updateProcessName: 'update:requestStagesContent',

    totalRows: 0,
    perPage: 50,
    currentPage: 1,
    sortBy: null,
    sortDesc: false,
    sortDirection: 'asc',
    filter: null,
    createLabor: true,

    fields: [],

    filters: {
      id: '',
      project_stage_duration_time_days: '',
      cost_for_all_days_sum: '',
      cost_for_offer_sum: '',
      project_stage: '',

      user_name_create: '',
      create_date: '',
      user_name_update: '',
      update_date: '',
    },

    priceLists_Items: [],
    price_list_id: null,
    items: [],
  }),

  created: function() {
    this.requestContentManager = new RequestContentManager();
    this.getRequestContent();


    this.setDefaultInterfaceData();
  },

  methods: {
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (parseInt(item.status) === 0) return 'table-deleted-row'
    },
    getRequestContent: function () {
      this.showCustomLoaderDialog = true;
      this.requestContentManager.getAllStagesByRequestId(this.request_id)
          .then( (response) => {
            if(response.data !== false){
              this.items = response.data.items;
              this.totalRows = response.data.items.length;
              this.price_list_id = response.data.price_list_id;
            }
            this.showCustomLoaderDialog = false;
          })
          .catch(function (error) {
            console.log(error);
          });
    },

    updateStagesLaborCosts: function(){
      this.showCustomLoaderDialog = true;

      var updateData = {
        items: this.items,
      }

      this.requestContentManager.updateStagesLaborCosts(updateData)
          .then( (response) => {
            if(response.data !== false){
              this.$eventHub.$emit(this.updateRequestProjectStageTrigger);
              // this.getRequestContent();
              this.showCustomLoaderDialog = false;
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
        { key: 'status', label: this.$store.state.t('Actions'), sortable: true},
        { key: 'id', sortable: true},
        { key: 'project_stage', label: this.$store.state.t('Project Stage'), sortable: true},
        { key: 'cost_for_all_days_sum', label: this.$store.state.t('Cost For All Days Sum'), sortable: true},
        { key: 'cost_for_offer_sum', label: this.$store.state.t('Cost For Offer Sum'), sortable: true},
        { key: 'project_stage_duration_time_days', label: this.$store.state.t('Project Stage Duration Time Days'), sortable: true},

        { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
        { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
        { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
        { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
      ];
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
