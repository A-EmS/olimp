<template>
  <div>
      <b-card v-if="getACL().list === true" :title="$store.state.t('Finance Classes')" class="main-card mb-4">
      <v-flex xs12>
        <v-treeview
                v-if="items.length > 0 && showTree === true"
                :items="items"
                :active.sync="active"
                :load-children="fetchFinanceClasses"
                :open.sync="open"
                :open-all="true"
                item-key="id"
                active-class="primary--text"
                class="grey lighten-5"
                transition
        >
            <template slot="label" slot-scope="props">
                <span :class="{ 'notLeafTree': !props.item.isLeaf }">{{ props.item.name }}</span> <span class="font-grey">#{{props.item.priority}}</span>
                <span>
                    <i v-if="getACL().update === true" v-on:mouseleave="toggleFont($event)" v-on:click="updateEntity(props.item)" v-on:mouseover="toggleFont($event)" class="lnr-pencil ml-4 font14 font-grey"></i>
                    <i v-if="getACL().create === true" v-on:mouseleave="toggleFont($event)" v-on:click="addEntity(props.item)" v-on:mouseover="toggleFont($event)" class="lnr-file-add ml-2 font14 font-grey"></i>
                    <i
                            @click="confirmDeleteRow(props.item)"
                            v-if="props.item.id !== 1 && props.item.isLeaf === true && getACL().delete === true"
                            v-on:mouseleave="toggleFont($event)"
                            v-on:mouseover="toggleFont($event)" class="lnr-trash ml-2 font14 font-grey">
                    </i>
                    <i v-if="props.item.id !== 1 && getACL().update === true" v-on:click="changeParentEntity(props.item)" v-on:mouseleave="toggleFont($event)" v-on:mouseover="toggleFont($event)" class="lnr-cloud-sync ml-2 font14 font-grey"></i>
                </span>
            </template>
        </v-treeview>
      </v-flex>

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
      <form-update
              :handlerUpdateProcessName="updateProcessName"
              :handlerUpdateOutputProcessName="updatedProcessName"
      ></form-update>
      <form-create
              :handlerCreateProcessName="createProcessName"
              :handlerCreateOutputProcessName="createdProcessName"
      ></form-create>
      <form-change-parent
              :handlerChangeParentProcessName="changeParentProcessName"
              :handlerChangeParentOutputProcessName="changedParentProcessName"
      ></form-change-parent>
  </div>
</template>

<script>
  import loadercustom from "../../../components/loadercustom";
  import confirmator from "../../../components/confirmator";

  import qs from "qs";
  import axios from "axios";
  import accessMixin from "../../../../mixins/accessMixin";
  import {FinanceClassesManager} from "../../../../managers/FinanceClassesManager";
  import FormUpdate from "./formUpdate";
  import FormChangeParent from "./formChangeParent";
  import FormCreate from "./formCreate";

  export default {
    components: {
        FormCreate,
        FormChangeParent,
        FormUpdate,
      loadercustom,
      confirmator,
    },

    mixins: [accessMixin],

    data: () => ({
      accessLabelId: 'financeClasses',
      showCustomLoaderDialog: false,
      customDialogfrontString: 'Please stand by',
      confirmDeleteString: '',
      showConfirmatorDialog: false,

      updateItemListEventName: 'updateList:financeClasses',

      createProcessName: 'create:financeClasses',
      createdProcessName: 'created:financeClasses',
      updateProcessName: 'update:financeClasses',
      updatedProcessName: 'updated:financeClasses',
      changeParentProcessName: 'changeParent:financeClasses',
      changedParentProcessName: 'changedParent:financeClasses',
      confirmatorInputProcessName: 'confirm:deleteFinanceClasses',
      confirmatorOutputProcessName: 'confirmed:deleteFinanceClasses',

        items: [],
        open: [],
        active: [],
        activeItem: {},
        addingProcess: false,
        selectedParentNode: false,
        showTree: true,
    }),

    created: function() {
        this.financeClassesManager = new FinanceClassesManager();
        this.loadACL(this.accessLabelId);
        this.getFinanceClasses();

        this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.deleteRow(data.id);
        });

        this.$eventHub.$on(this.createdProcessName, (data) => {
            this.showCustomLoaderDialog = true;
            var self = this;
            this.financeClassesManager.create({parentNodeId:data.parentNodeId, name: data.createdItemName, priority: data.priority})
                .then( (response) => {
                    if (response.data !== false){
                        if (!response.data.error){
                            if(self.open.indexOf(data.parentNodeId) === -1) {
                                self.open.push(data.parentNodeId);
                            }
                            //==================================================
                            // var newNode = {
                            //     id: response.data.id,
                            //     isLeaf: true,
                            //     isRoot: false,
                            //     name: response.data.name,
                            //     priority: response.data.priority,
                            // };

                            // if (!this.activeItem.children) {
                            //     this.$set(this.activeItem, "children", [newNode]);
                            // } else {
                            //     this.activeItem.children.unshift(newNode)
                            // }
                            //==================================================

                            this.financeClassesManager.getChildrenByNodeId(this.activeItem.id)
                                .then( (response) => {
                                    if(response.data !== false){
                                        this.$set(this.activeItem, "children", response.data.items);
                                        this.reloadTree();
                                        this.showCustomLoaderDialog = false;
                                    }
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        } else {
                            this.openErrorDialog(response.data.error);
                        }
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        });

        this.$eventHub.$on(this.updatedProcessName, (data) => {
            this.showCustomLoaderDialog = true;
            this.financeClassesManager.update({id:data.item.id, name: data.item.name, priority: data.item.priority})
                .then( (response) => {
                    this.showCustomLoaderDialog = false;
                    if (response.data !== false){
                        if (!response.data.error){

                            this.financeClassesManager.getChildrenByNodeId(this.selectedParentNode.id)
                                .then( (response) => {
                                    if(response.data !== false){
                                        this.$set(this.selectedParentNode, "children", response.data.items);
                                        this.reloadTree();
                                        this.showCustomLoaderDialog = false;
                                    }
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });

                        } else {
                            this.openErrorDialog(response.data.error);
                        }
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        });

        this.$eventHub.$on(this.changedParentProcessName, (data) => {
            this.showCustomLoaderDialog = true;
            this.customDialogfrontString = 'Moving...';
            this.financeClassesManager.move(data)
                .then( (response) => {
                    this.showCustomLoaderDialog = false;
                    if (response.data !== false){
                        if (!response.data.error){
                            window.location.reload();
                        } else {
                            this.openErrorDialog(response.data.error);
                        }
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        });
    },

    methods: {
        reloadTree() {
            this.showTree = false;
            this.addingProcess = false;
            this.$nextTick(() => {
                this.showTree = true
            })
        },
        updateEntity: function(item){
            this.$eventHub.$emit(this.updateProcessName, item);
            this.selectedParentNode = false;
            this.selectParentNode(this.items, item.id);
        },
        addEntity: function(item){
            this.activeItem = item;
            this.addingProcess = true;
            this.$eventHub.$emit(this.createProcessName, item)
            if(this.open.indexOf(item.id) === -1 && !item.isLeaf) {
                this.open.push(item.id);
            }
        },
        changeParentEntity: function(item){
            this.$eventHub.$emit(this.changeParentProcessName, item)
        },
        async fetchFinanceClasses (item) {
          var self = this;
          return this.financeClassesManager.getChildrenByNodeId(item.id)
            .then( (response) => {
                if(response.data !== false){
                    if(self.open.indexOf(item.id) === -1 && !item.isLeaf && self.addingProcess) {
                        self.open.push(item.id);
                        self.addingProcess = false;
                    }
                    item.children = response.data.items;
                }
            })
            .catch(function (error) {
                console.log(error);
            });
      },

    toggleFont: function (e) {
        e.currentTarget.classList.toggle('font-grey');
        e.currentTarget.classList.toggle('font18');
    },

      getFinanceClasses: function () {
        this.showCustomLoaderDialog = true;
        this.financeClassesManager.getInitNodes()
          .then( (response) => {
            if(response.data !== false){
                this.items = response.data.items;
                this.showCustomLoaderDialog = false;
            }
          })
          .catch(function (error) {
            console.log(error);
          });
      },

      confirmDeleteRow: function(item){
        this.$eventHub.$emit(this.confirmatorInputProcessName, {
          titleString: this.$store.state.t('Deleting') + '...',
          confirmString: this.$store.state.t('Confirm delete') +  ' ' + this.$store.state.t('Finance Classes') +'..'+item.name,
          idToConfirm: item.id
        });
      },

      deleteRow: function(id){
        this.showCustomLoaderDialog = true;
        this.customDialogfrontString= this.$store.state.t('Deleting') + '...'+id;
        this.financeClassesManager.delete({id:id})
                .then( (response) => {
                  if(response.data !== false){
                    if(response.data.status === true){

                    this.deleteNode(this.items, id);

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
        openErrorDialog(message, time){
            var dialogTime = time || 5000;
            this.customDialogfrontString = this.$store.state.t(message);
            this.showCustomLoaderDialog = true;
            setTimeout(() => {
                this.showCustomLoaderDialog = false;
            }, dialogTime);
        },

        deleteNode: function (array, id) {
          for (var i = 0; i < array.length; ++i) {
              var obj = array[i];
              if (obj.id === id) {
                  array.splice(i, 1);
                  return true;
              }
              if (obj.children) {
                  if (this.deleteNode(obj.children, id)) {
                      if (obj.children.length === 0) {
                          delete obj.children;
                          array[i].isLeaf = true
                      }
                      return true;
                  }
              }
          }
        },

        selectParentNode: function (array, id) {
            for (var i = 0; i < array.length; ++i) {
                var obj = array[i];
                if (obj.id === id) {
                    return true;
                }
                if (obj.children) {
                    if (this.selectParentNode(obj.children, id)) {
                        if (this.selectedParentNode === false) {
                            this.selectedParentNode = obj;
                        }
                        return true;
                    }
                }
            }
        },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.confirmatorOutputProcessName);
      this.$eventHub.$off(this.updateItemListEventName);
      this.$eventHub.$off(this.createdProcessName);
      this.$eventHub.$off(this.updatedProcessName);
      this.$eventHub.$off(this.changedParentProcessName);
    },
  }
</script>
<style type="text/css">
    .font14 {
        font-size: 14px;
        cursor: pointer;
    }
    .notLeafTree {
        /*font-weight: bold;*/
        text-decoration: underline;
    }
    .font18 {
        font-size: 18px !important;
    }
    .font-grey {
        color: lightgrey;
    }
    .v-treeview-node__root {
        min-height: 27px !important;
    }
    .v-treeview-node--leaf {
        min-height: 27px !important;
    }
</style>