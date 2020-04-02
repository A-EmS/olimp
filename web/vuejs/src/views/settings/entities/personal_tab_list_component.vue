<template>
    <div>
        <b-card :title="getCardTitle()" class="main-card mb-4">
            <form_component :entity_settled_id="contractorRefId" :createProcessNameTrigger="createProcessName" :updateProcessNameTrigger="updateProcessName" :updateItemListNameTrigger="updateItemListEventName" ></form_component>
            <individuals_form_component showListEventName="showList:individual" createProcessNameTrigger="none" updateProcessNameTrigger="update:individual" updateItemListNameTrigger="none" ></individuals_form_component>

            <button style="margin-bottom: 10px" v-if="notOriginalPage && showAdditionalCreatingButton" v-on:click="createNew()" type="button" class="btn-shadow d-inline-flex align-items-center btn btn-success">
                {{$store.state.t('Add Entity')}}
            </button>

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

                <template slot="actions" slot-scope="row">
                    <table>
                        <tr>
                            <td v-if="notOriginalPage"><i class='lnr-pencil' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRowIndividual(parseInt(row.item.individual_id))"> </i></td>
                            <td v-else><i class='lnr-pencil' size="sm" style="cursor: pointer; font-size: large" @click.stop="" @click="updateRow(parseInt(row.item.id))"> </i></td>
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

    import loadercustom from "../../components/loadercustom";
    import confirmator from "../../components/confirmator";
    import form_component from "../personal/form_component";
    import individuals_form_component from "../individuals/form_component"
    import {PM} from "../../../managers/PersonalManager";
    import qs from "qs";
    import Individuals from "../individuals/Individuals";

    var moment = require('moment');

  export default {
    components:{
        Individuals,
        individuals_form_component,
        moment,
        loadercustom,
        confirmator,
        form_component,
        PM,
    },
    props: {
        exceptedFields: {type: Array, require: false},
        expectedFields: {type: Array, require: false},
        contractorRefId: {type: Number, require: false},
        contractorIsEntity: {type: Number, require: false},
        showCardTitle: {type: Boolean, require: true, default: true},
        notOriginalPage: {type: Boolean, require: true, default: false},
    },

    data () {
      return {
          showAdditionalCreatingButton: true,
          showCustomLoaderDialog: false,
          customDialogfrontString: 'Please stand by',
          confirmDeleteString: '',
          showConfirmatorDialog: false,

          updateItemListEventName: 'updateList:Personal',
          createProcessName: 'create:personal',
          updateProcessName: 'update:personal',
          confirmatorInputProcessName: 'confirm:personal',
          confirmatorOutputProcessName: 'confirmed:personal',

          totalRows: 0,
          perPage: 50,
          currentPage: 1,
          sortBy: null,
          sortDesc: false,
          sortDirection: 'asc',
          filter: null,

          fields: [],

          filters: {
              individual_id: '',
              full_name: '',
              position: '',
              notice: '',
          },

          items: [],
      }
    },

    created() {
        this.personalManager = new PM();
        this.getDataForList();

        this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
            this.deleteRow(data.id);
        });

        this.$eventHub.$on(this.updateItemListEventName, (data) => {
            this.getDataForList();
        });

        this.setDefaultInterfaceData();
    },

    methods: {
        createNew: function (){
            this.showAdditionalCreatingButton = false;
            this.$eventHub.$emit(this.createProcessName, {refId: this.contractorRefId, isEntity: this.contractorIsEntity});
        },

        getCardTitle: function () {
            if (this.showCardTitle){
                return this.$store.state.t('Entity');
            }

            return '';
        },

        getDataForList: function () {

            this.showAdditionalCreatingButton = true;
            this.personalManager.getDataForEntityTab(this.contractorRefId).then((response) => {
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

        updateRowIndividual: function(individual_id){
            window.scrollToTop();
            this.$eventHub.$emit('update:individual', {id: individual_id});
        },

        confirmDeleteRow: function(id, name){
            this.$eventHub.$emit(this.confirmatorInputProcessName, {
                titleString: this.$store.state.t('Deleting') + '...',
                confirmString: this.$store.state.t('Confirm delete') +  ' ' + this.$store.state.t('Personal') +'..'+name,
                idToConfirm: id
            });
        },

        deleteRow: function(id){
            this.showCustomLoaderDialog = true;
            this.customDialogfrontString= this.$store.state.t('Deleting') + '...'+id;

            axios.post(window.apiDomainUrl+'/personal/delete', qs.stringify({id:id}))
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
                { key: 'individual_id', label:'id', sortable: true},
                { key: 'actions', label: this.$store.state.t('Actions')},
                { key: 'full_name', label: this.$store.state.t('Full Name'), sortable: true},
                { key: 'position', label: this.$store.state.t('Position'), sortable: true},
                { key: 'notice', label: this.$store.state.t('Notice'), sortable: false},
            ];

            if (typeof this.exceptedFields !== 'undefined' && this.exceptedFields.length > 0){
                this.exceptedFields.forEach((key) => {
                    var i = this.fields.findIndex(obj => obj.key === ''+key);
                    if (typeof i !== 'undefined'){
                        this.fields.splice(i, 1);
                    }
                });
            }

            if (typeof this.expectedFields !== 'undefined' && this.expectedFields.length > 0){
                var tmpArray = [];
                this.expectedFields.forEach((key) => {
                    var i = this.fields.findIndex(obj => obj.key === ''+key);
                    if (typeof i !== 'undefined'){
                        tmpArray.push(this.fields[i]);
                    }
                });
                this.fields = tmpArray;
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
