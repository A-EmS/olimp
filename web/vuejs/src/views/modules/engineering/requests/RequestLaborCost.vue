<template>
  <div>
    <b-card class="main-card mb-4">
      <v-btn color="success" @click="updateLaborCosts">{{$store.state.t('Save')}}</v-btn>
      <v-btn style="background-color: #343a40; color: white" @click="openPatterner">{{$store.state.t('Generate Commercial Offering')}}</v-btn>
      <b-row class="mb-3">
        <b-col md="6" class="my-1">
          <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
        </b-col>

        <b-col md="6" class="my-1" style="text-align: right; color: grey">
          {{paginationHeader()}}
        </b-col>
      </b-row>
      <v-autocomplete
          v-model="price_list_id"
          :items="priceLists_Items"
          item-value="id"
          item-text="name"
          :label="$store.state.t('Price List')"
          @change="onPriceListSelect()"
      ></v-autocomplete>
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

        <template slot="duration_time_days" slot-scope="row">
          <input v-model="row.item.duration_time_days" type="number" min="0" step="0.01" @change="recalculateItem(row.item)">
        </template>
        <template slot="cost_for_day" slot-scope="row">
          <input v-model="row.item.cost_for_day" type="number" min="0" step="0.01" @change="recalculateItem(row.item)">
        </template>
        <template slot="extra_charge" slot-scope="row" prefix="btn">
          <input v-model="row.item.extra_charge" type="number" min="0" step="0.01" @change="recalculateItem(row.item)">
          <a class="apply-to-all-extra-charge" href="#" @click.stop.prevent="" @click="applyExtraChargeToAllItems(row.item)">
            {{$store.state.t('Apply to all')}}
          </a>
        </template>
        <template slot="notice" slot-scope="row">
          <textarea-autosize class="form-control"
                             v-model="row.item.notice"
                             :min-height="30"
                             :max-height="200"
          ></textarea-autosize>
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
      <v-btn color="success" @click="updateLaborCosts">{{$store.state.t('Save')}}</v-btn>
    </b-card>


    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
    <patterner
        :handlerInputProcessName="patternerInputProcessName"
        :handlerOutputProcessName="patternerOutputProcessName">
    </patterner>
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
  import {PriceListsManager} from "@/managers/PriceListsManager";
  import {PricesManager} from "@/managers/PricesManager";
  import VueTextareaAutosize from 'vue-textarea-autosize'
  import Vue from "vue";
  import Patterner from "@/views/components/patterner";
  import constantsMixin from "@/mixins/constantsMixin";
  Vue.use(VueTextareaAutosize);

  export default {
    components: {
      Patterner,
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
    },

    data: () => ({
      accessLabelId: 'requests',
      showPatternDialog: false,
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,
      patternerInputProcessName: 'patternerInputProcess',
      patternerOutputProcessName: 'patternerOutputProcess',

      updateItemListEventName: 'updateList:requestContent',
      createProcessName: 'create:requestContent',
      updateProcessName: 'update:requestContent',

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
        project_part: '',
        duration_time_days: '',
        cost_for_day: '',
        cost_for_all_days: '',
        cost_for_offer: '',
        notice: '',
        extra_charge: '',
        project_part_code: '',
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
      this.priceListsManager = new PriceListsManager();
      this.pricesManager = new PricesManager();
      this.getRequestContent();
      this.getPriceListsForSelect();

      this.setDefaultInterfaceData();
    },

    methods: {
      openPatterner() {
        this.$eventHub.$emit(this.patternerInputProcessName, {request_id: this.request_id, country_id: this.country_id, price_list_id: parseInt(this.price_list_id), own_company_id: this.own_company_id, document_type_id: this.constants.documentScenarioIdCommercialOffering});
      },
      rowClass(item, type) {
        if (!item || type !== 'row') return
        if (parseInt(item.status) === 0) return 'table-deleted-row'
      },
      createLaborCosts: function () {
        this.showCustomLoaderDialog = true;
        this.requestContentManager.createLaborCosts({request_id: this.request_id, price_list_id: this.price_list_id, country_id: this.country_id})
            .then( (response) => {
              if(response.data !== false){
                this.getRequestContent();
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      updateItemsOnPrice: function (){
        this.pricesManager.getDataByPriceListId(this.price_list_id)
            .then( (response) => {
              if(response.data !== false){
                var items = response.data.items || [];

                items.forEach((item) => {
                  var currentIndex = this.items.indexOf(this.items.find(obj => obj.project_part_id === item.project_part_id));
                  if (typeof this.items[currentIndex] != 'undefined') {
                    this.items[currentIndex]['cost_for_day'] = Math.floor((parseFloat(item.price)) * 100) / 100;
                    this.items[currentIndex]['price_list_id'] = this.price_list_id;
                  }
                });

                this.recalculateRequestPrice();
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      recalculateRequestPrice: function (){
        this.items.forEach((item) => {
          this.recalculateItem(item);
        });
      },
      recalculateItem: (item) => {
        item.cost_for_all_days = Math.floor((item.duration_time_days * item.cost_for_day) * 100) / 100;
        item.cost_for_offer = Math.floor((item.cost_for_all_days * item.extra_charge) * 100) / 100;
      },
      applyExtraChargeToAllItems: function (processableItem){
        this.items.forEach((item) => {
          item.extra_charge = processableItem.extra_charge;
        });

        this.recalculateRequestPrice();
      },
      onPriceListSelect: function () {
        if (this.createLabor === true) {
          this.createLaborCosts();
        } else {
          this.updateItemsOnPrice();
        }
      },
      getPriceListsForSelect: function () {
        this.priceListsManager.getAllForSelect()
            .then( (response) => {
              if(response.data !== false){
                this.priceLists_Items = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getRequestContent: function () {
        this.showCustomLoaderDialog = true;
        this.requestContentManager.getAllByRequestId(this.request_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.items = response.data.items;
                    this.totalRows = response.data.items.length;
                    this.price_list_id = response.data.price_list_id;
                    if (response.data.price_list_id > 0){
                      this.price_list_id = this.price_list_id.toString()
                    }

                    if (this.price_list_id == null) {
                      this.createLabor = true;
                    } else {
                      this.createLabor = false;
                    }
                  }
                  this.showCustomLoaderDialog = false;
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      updateLaborCosts: function(){
        this.showCustomLoaderDialog = true;

        var updateData = {
          items: this.items,
        }

        this.requestContentManager.updateLaborCosts(updateData)
            .then( (response) => {
              if(response.data !== false){
                this.getRequestContent();
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
          { key: 'project_part', label: this.$store.state.t('Project Part'), sortable: true},
          { key: 'project_part_code', label: this.$store.state.t('Code'), sortable: true},
          { key: 'duration_time_days', label: this.$store.state.t('Duration Time Days'), sortable: true},
          { key: 'cost_for_day', label: this.$store.state.t('Cost For Day'), sortable: true},
          { key: 'cost_for_all_days', label: this.$store.state.t('Cost For All Days'), sortable: true},
          { key: 'extra_charge', label: this.$store.state.t('Extra Charge'), sortable: true},
          { key: 'cost_for_offer', label: this.$store.state.t('Cost For Offer'), sortable: true},

          { key: 'notice', label: this.$store.state.t('Notice'), sortable: true},

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

        // filtered.map(item => {
        //   item._rowVariant  = 'success';
        //   return item
        // })

        this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    }
  }
</script>
