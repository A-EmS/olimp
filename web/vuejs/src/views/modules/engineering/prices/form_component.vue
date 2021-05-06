<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >
          <v-autocomplete
                  v-model="price_list_id"
                  :items="priceLists_Items"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Price List')"
                  :readonly="rowId > 0"
                  @change="onPriceListSelect()"
          ></v-autocomplete>

          <v-autocomplete
              v-model="country_id"
              :items="countries_Items"
              item-value="id"
              item-text="name"
              :label="$store.state.t('Country')"
              v-if="price_list_id > 0"
              @change="onCountrySelect()"
          ></v-autocomplete>

          <b-card v-if="items.length > 0" class="main-card mb-4">
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
                      v-if="field.key !== 'actions' && field.key !== 'project_stage_code'"
                      v-model="filters[field.key]"
                      style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                      class="col-md-12"
                  >

                  <select
                      v-if="field.key=='project_stage_code'"
                      v-model="filters['project_stage_code']"
                      style="background-color: white; border: 1px solid lightgrey; border-radius: 4px;"
                      class="col-md-12"
                  >
                    <option value="">{{$store.state.t('All Codes')}}</option>
                    <option v-for="item_code in stagesCodes_Items" :value="item_code.code">{{item_code.code}}</option>
                  </select>

                </td>
              </template>

              <template slot="price" slot-scope="row">
                <input v-model="row.item.price" type="number" min="0" step="0.01" @change="onPriceChange(row.item.id, row.item.price)">
              </template>

              <template slot="create_date" slot-scope="row">
                {{row.item.create_date | dateFormat}}
              </template>

              <template slot="update_date" slot-scope="row">
                {{row.item.update_date | dateFormat}}
              </template>

            </b-table>

            <b-row>
              <b-col md="6" class="my-1">
                <b-pagination :total-rows="totalRows" :per-page="perPage" v-model="currentPage" class="my-0" />
              </b-col>
            </b-row>
          </b-card>

          <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
          <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>

    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../../Layout/Components/DemoCard';
  import loadercustom from "../../../components/loadercustom";

  import { validationMixin } from 'vuelidate'
  import {PriceListsManager} from "@/managers/PriceListsManager";
  import {PricesManager} from "@/managers/PricesManager";
  import {CountriesManager} from "@/managers/CountriesManager";
  import {ProjectStagesManager} from "@/managers/ProjectStagesManager";
  import {ProjectPartsManager} from "@/managers/ProjectPartsManager";

  var moment = require('moment');

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
      moment
    },

    mixins: [validationMixin],

    validations: {

    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',
        confirmDeleteString: '',
        showConfirmatorDialog: false,

        showDialog: false,
        valid: true,
        header: '',

        totalRows: 0,
        perPage: 100,
        currentPage: 1,
        sortBy: null,
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,

        updateItems: [],
        fields: [],

        filters: {
          id: '',
          project_stage_name: '',
          project_part_name: '',
          price: '',
          priority: '',
          project_stage_code: '',
          project_part_code: '',

          user_name_create: '',
          create_date: '',
          user_name_update: '',
          update_date: '',
        },

        items: [],

        rowId: 0,

        countries_Items: [],
        country_id: null,

        stagesCodes_Items: [],
        partsCodes_Items: [],
        priceLists_Items: [],
        price_list_id: null,
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {
      this.priceListsManager = new PriceListsManager();
      this.pricesManager = new PricesManager();
      this.countriesManager = new CountriesManager();
      this.projectStagesManager = new ProjectStagesManager();
      this.projectPartsManager = new ProjectPartsManager();


      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.pricesManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.price_list_id = response.data.price_list_id;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.header = this.$store.state.t('Updating')+'...';
        this.showDialog = true;
      });

      this.setDefaultInterfaceData();
    },

    methods: {
      initFormComponent: function(){
        this.getCountriesForSelect();
        this.getPriceListsForSelect();
      },
      getStagesCodes: function () {
        this.projectStagesManager.getAllCodesForSelectAccordingCountry(this.country_id)
            .then( (response) => {
              if(response.data !== false){
                this.stagesCodes_Items = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getPartsCodes: function () {
        this.projectPartsManager.getAllCodesForSelectAccordingCountry(this.country_id)
            .then( (response) => {
              if(response.data !== false){
                this.partsCodes_Items = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      onPriceChange: function (itemId, itemPrice) {

        var currentIndex = this.updateItems.indexOf(this.updateItems.find(obj => obj.id === itemId));
        delete(this.updateItems[currentIndex]);
        this.updateItems = this.updateItems.filter(function (el) {
          return el != '';
        });

        this.updateItems.push({
          id: itemId,
          price: itemPrice,
        })

      },
      onPriceListSelect: function () {
        this.create();
      },
      onCountrySelect: function () {
        this.pricesManager.getDataByCountryAndPriceList(this.country_id, this.price_list_id)
            .then( (response) => {
              if(response.data !== false){
                this.items = response.data.items;
                this.getStagesCodes();
                this.getPartsCodes();
              }
            })
            .catch(function (error) {
              console.log(error);
            });
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
      getCountriesForSelect: function () {
        this.countriesManager.getForSelectAccordingProjectParts()
                .then( (response) => {
                  if(response.data !== false){
                    this.countries_Items = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },

      submit: function () {
        this.$v.$touch();
        if (!this.$v.$invalid) {
          if (this.rowId === 0){
            this.create();
          } else {
            this.update();
          }
        }
      },
      create: function(){

        var createData = {
          price_list_id: this.price_list_id
        };

        this.showCustomLoaderDialog = true;
        this.pricesManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (response.data.error) {
                      // this.showCustomLoaderDialog = true;
                      // this.customDialogfrontString = this.$store.state.t(response.data.error);
                      // setTimeout(() => {
                      //   this.showCustomLoaderDialog = false;
                      // }, 5000);

                      // this.$eventHub.$emit(this.updateProcessNameTrigger, {id: response.data.id});
                    } else {
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      // this.showDialog = false;
                    }

                    this.showCustomLoaderDialog = false;
                    this.$eventHub.$emit(this.updateProcessNameTrigger, {id: response.data.id});
                  }
                })
                .catch((error) => {
                  this.showCustomLoaderDialog = true;
                  this.customDialogfrontString = this.$store.state.t(error);
                  setTimeout(() => {
                    this.showCustomLoaderDialog = false;
                  }, 5000);
                  this.setDefaultData();
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          updateItems: this.updateItems,
        };

        this.pricesManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    if (response.data.error) {
                      this.showCustomLoaderDialog = true;
                      this.customDialogfrontString = this.$store.state.t(response.data.error);
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, 5000);
                    } else {
                      this.showDialog = false;
                      this.setDefaultData();
                    }
                  }
                })
                .catch((error) => {
                  this.showCustomLoaderDialog = true;
                  this.customDialogfrontString = this.$store.state.t(error);
                  setTimeout(() => {
                    this.showCustomLoaderDialog = false;
                  }, 5000);
                  console.log(error);
                  this.setDefaultData();
                });
      },
      cancel () {
        this.$v.$reset();
        this.showDialog = false;
        this.setDefaultData();
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

      setDefaultData () {
        this.price_list_id = 0;
        this.country_id = 0;
        this.rowId = 0;
        this.items = [];
        this.updateItems = [];
      },
      setDefaultInterfaceData: function () {
        this.customDialogfrontString = this.$store.state.t('Please stand by');
        this.fields = [
          { key: 'id', sortable: true},
          { key: 'priority', label: this.$store.state.t('Priority'), sortable: true},
          { key: 'project_stage_name', label: this.$store.state.t('Project Stage'), sortable: true},
          { key: 'project_stage_code', label: this.$store.state.t('Project Stage Code'), sortable: true},
          { key: 'project_part_name', label: this.$store.state.t('Project Part'), sortable: true},
          { key: 'project_part_code', label: this.$store.state.t('Project Part Code'), sortable: true},
          { key: 'price', label: this.$store.state.t('Price'), sortable: true},


          { key: 'user_name_create', label: this.$store.state.t('User Name Create'), sortable: true},
          { key: 'create_date', label: this.$store.state.t('Create Date'), sortable: true},
          { key: 'user_name_update', label: this.$store.state.t('User Name Update'), sortable: true},
          { key: 'update_date', label: this.$store.state.t('Update Date'), sortable: true},
        ]
      },
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
        })
        .filter(item => {
          if(
              (item.project_stage_code === this.filters['project_stage_code']) ||
              (!this.filters['project_stage_code'] || this.filters['project_stage_code'] <= 0)
          ) {
            return item;
          }
        })
        ;

        this.totalRows = filtered.length;
        return filtered.length > 0 ? filtered : [];
      }
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
