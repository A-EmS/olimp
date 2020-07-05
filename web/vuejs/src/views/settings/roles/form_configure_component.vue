<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="$store.state.t('Configure Role') +' -> '+this.roleName" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >

          <table aria-busy="false" aria-colcount="8" class="table b-table table-striped table-bordered table-sm border" id="__BVID__67">
            <thead role="rowgroup" class="">
            <tr role="row">
              <th tabindex="0" role="columnheader" scope="col" aria-colindex="1" aria-label="Click to sort Ascending" aria-sort="none" class="">
                {{$store.state.t('Access Item')}}
              </th>
              <th v-for="tableType in tableTypes" :key="tableType.name" tabindex="0" role="columnheader" scope="col" aria-colindex="2" class="">
                {{$store.state.t(ucFirst(tableType.name))}}
              </th>
            </tr>
            </thead>
            <tbody role="rowgroup" class="">
              <tr v-for="tableItem in tableItems" role="row" class="">
                <td role="cell" aria-colindex="0" class=""><b>{{$store.state.t(tableItem.title_alias)}}</b></td>
                <td v-for="tableType in tableTypes" :key="tableType.name" role="cell" class="">
                  <b-checkbox type="checkbox" :id="tableItem.id+'-'+tableType.id" class="accesssableItem"
                    :checked="typeof(roleConfig[tableItem.id]) !== 'undefined' && typeof(roleConfig[tableItem.id][tableType.id]) !== 'undefined' && roleConfig[tableItem.id][tableType.id] === true"
                  >
                    {{$store.state.t(ucFirst(tableType.name))}}
                  </b-checkbox>
                </td>
              </tr>
            </tbody>
          </table>

          <v-btn color="success" @click="submit">{{$store.state.t('Update')}}</v-btn>
          <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>

    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';
  import loadercustom from "../../components/loadercustom";


  import { validationMixin } from 'vuelidate'
  import qs from "qs";

  import {RolesManager} from "../../../managers/RolesManager";
  import {AccessGridManager} from "../../../managers/AccessGridManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {

    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',

        showDialog: false,
        valid: true,
        header: '',
        roleName: '',
        rowId: 0,
        name: '',

        roleConfig: {},
        tableItems: [],
        tableTypes: [],
      }
    },
    props: {
      configureProcessNameTrigger: {type: String, require: false},
    },
    created() {
      this.rolesManager = new RolesManager();
      this.accessGridManager = new AccessGridManager();

      this.$eventHub.$on(this.configureProcessNameTrigger, (data) => {
        this.setDefaultData();
        this.rowId = data.id;
        this.rolesManager.getById(data.id)
                .then((response) => {
                  this.roleName = response.data.name;
                });
        this.accessGridManager.getByRoleId(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.roleConfig = response.data;
                  }
                }).then(() => {

                  this.accessGridManager.getTable().then((response) => {
                            if(response.data !== false){

                              this.tableItems = response.data.tableItems;
                              this.tableTypes = response.data.tableTypes;
                            }
                          })
                })
                .catch(function (error) {
                  console.log(error);
                });

        this.showDialog = true;
      });
    },

    methods: {
      ucFirst: function (str) {
        if (!str) return str;

        return str[0].toUpperCase() + str.slice(1);
      },

      submit: function () {
        this.update();
      },

      update: function(){

        var tableItems = this.tableItems;
        var tableTypes = this.tableTypes;
        var checkedConfig = [];

        tableItems.forEach(function (tableItem, indexItem) {
          tableTypes.forEach(function (tableType, indexType) {
            var currentCheckbox = document.getElementById(tableItem.id+ '-' + tableType.id);
            if (currentCheckbox.checked == true) {
              checkedConfig.push({
                access_item_id: tableItem.id,
                access_type_id: tableType.id
              });
            }
          });
        });

        var updateData = {
          checkedConfig: checkedConfig,
          id: this.rowId
        };

        this.openErrorDialog('Updating...', 2000);

        this.accessGridManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      // this.$eventHub.$emit(this.configureProcessNameTrigger);
                    } else {
                      this.openErrorDialog(response.data.error);
                    }
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      cancel () {
        this.$v.$reset();
        this.showDialog = false;
        if (typeof this.$parent.showAdditionalCreatingButton !== 'undefined'){
          this.$parent.showAdditionalCreatingButton = true;
        }
      },

      openErrorDialog(message, time){
        var dialogTime = time || 5000;
        this.customDialogfrontString = this.$store.state.t(message);
        this.showCustomLoaderDialog = true;
        setTimeout(() => {
          this.showCustomLoaderDialog = false;
        }, dialogTime);
      },

      setDefaultData () {
        this.roleConfig = {};
        this.tableItems = [];
        this.tableTypes = [];
        this.rowId = 0;
      }
    },

    computed: {

    },

    beforeDestroy () {
      this.$eventHub.$off(this.configureProcessNameTrigger);
    },
  }
</script>
