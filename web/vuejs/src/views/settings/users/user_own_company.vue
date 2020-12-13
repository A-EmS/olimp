<template>
  <div>
      <v-autocomplete
          v-model="own_company_id"
          :items="ownCompanyItems"
          item-value="id"
          item-text="company"
          :label="$store.state.t('Own Company')"
          required
          @change="saveToUser()"
      ></v-autocomplete>

    <hr />

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
              <td><i class='lnr-trash' size="sm" style="cursor: pointer; font-size: large; color: red" @click.stop="" @click="deleteOwnCompany(parseInt(row.item.id))"> </i></td>
            </tr>
          </table>
        </template>
      </b-table>
    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import {UserOwnCompaniesManager} from "@/managers/UserOwnCompaniesManager";
  import {OwnCompaniesManager} from "@/managers/OwnCompaniesManager";
  import loadercustom from "../../components/loadercustom";

  export default {
    components: {
      loadercustom,
    },
    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',

        own_company_id: null,
        ownCompanyItems: [],
        userOwnCompanyItems: [],

        totalRows: 0,
        perPage: 500,
        currentPage: 1,
        sortBy: null,
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,

        fields: [],

        filters: {
          company: '',
        },

        items: [],
      }
    },
    props: {
      userId: {type: Number, require: true},
    },
    mounted() {
      this.ownCompaniesManager = new OwnCompaniesManager();
      this.userOwnCompaniesManager = new UserOwnCompaniesManager();

      this.getUserOwnCompanies();
      this.getOwnCompanies();
      this.setDefaultInterfaceData();
    },
    methods: {
      saveToUser: function () {
        this.showCustomLoaderDialog = true;
        let createUserOwnCompaniesData = {
          user_id: this.userId,
          own_company_id: this.own_company_id,
        };

        this.userOwnCompaniesManager.create(createUserOwnCompaniesData)
            .then( (response) => {
              if(response.data !== false){
                this.userOwnCompanyItems = response.data.items;
                this.own_company_id = null;
              }
            })
            .then(() => {
              this.getUserOwnCompanies();
              this.$eventHub.$emit('user_settings:refresOwnCompanies');
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      getUserOwnCompanies: function (){
        this.showCustomLoaderDialog = true;
        this.userOwnCompaniesManager.getAllByUserId(this.userId)
            .then((response) => {
              this.showCustomLoaderDialog = false;
              this.items = response.data.items;
            })
      },
      getOwnCompanies: function () {
        this.ownCompaniesManager.getAll()
            .then( (response) => {
              if(response.data !== false){
                this.showCustomLoaderDialog = false;
                this.ownCompanyItems = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      deleteOwnCompany: function (id){
        this.userOwnCompaniesManager.deleteRow(id)
        .then(() => {
          this.getUserOwnCompanies()
          this.$eventHub.$emit('user_settings:refresOwnCompanies');
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
        this.fields = [
          { key: 'actions', label: this.$store.state.t('Actions')},
          { key: 'company', label: this.$store.state.t('Company')},
        ]
      }
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
    },

    beforeDestroy () {

    },
  }
</script>
