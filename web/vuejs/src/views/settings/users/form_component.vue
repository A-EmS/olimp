<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="$store.state.t('Roles for User')" subheading="">

        <table aria-busy="false" aria-colcount="8" class="table b-table table-striped table-bordered table-sm border" id="__BVID__67">
          <tbody role="rowgroup" class="">
          <tr v-for="rolesBlock in rolesItems" role="row" class="">
            <td v-for="role in rolesBlock" :key="role.name" role="cell" class="">
              <b-checkbox type="checkbox" :id="'role_'+role.id" class="roleItem"
                          :checked="userRolesIds.indexOf(role.id) !== -1"
              >
                {{role.name}}
              </b-checkbox>
            </td>
          </tr>
          </tbody>
        </table>

      </demo-card>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >

          <v-text-field
                  v-model="user_real"
                  :error-messages="user_realErrors"
                  :counter="250"
                  :label="$store.state.t('User Name')"
                  required
                  @input="$v.user_real.$touch()"
                  @blur="$v.user_real.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="user_name"
                  :error-messages="user_nameErrors"
                  :counter="250"
                  :label="$store.state.t('Login')"
                  required
                  @input="$v.user_name.$touch()"
                  @blur="$v.user_name.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="user_pwd"
                  :error-messages="user_pwdErrors"
                  :counter="250"
                  :label="$store.state.t('Password')"
                  @input="$v.user_pwd.$touch()"
                  @blur="$v.user_pwd.$touch()"
          ></v-text-field>
          <v-textarea
                  v-model="notice"
                  :label="$store.state.t('Notice')"
          ></v-textarea>

          <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
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
  import { required, maxLength } from 'vuelidate/lib/validators'
  import qs from "qs";

  import {RolesManager} from "../../../managers/RolesManager";
  import {UsersManager} from "../../../managers/UsersManager";
  import {UserRolesManager} from "../../../managers/UserRolesManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {
      user_name: { required, maxLength: maxLength(250) },
      user_pwd: { required, maxLength: maxLength(250) },
      user_real: { required, maxLength: maxLength(250) },
    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',

        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,

        user_name: '',
        user_pwd: '',
        user_real: '',
        notice: '',

        rolesItems: [],
        userRolesIds: [],
        originalRolesItems: []
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {
      this.rolesManager = new RolesManager();
      this.usersManager = new UsersManager();
      this.userRolesManager = new UserRolesManager();



      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.getRoles();
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.getRoles();
        this.usersManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.user_id;
                    this.user_name = response.data.user_name;
                    this.user_pwd = '';
                    this.user_real = response.data.user_real;
                    this.notice = response.data.notice;
                    this.getUserRolesIds(response.data.user_id);
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.header = this.$store.state.t('Updating')+'...';
        this.showDialog = true;
      });

    },

    methods: {
      getRoles: function () {
        this.rolesManager.getAllForUserForm()
                .then( (response) => {
                  if(response.data !== false){
                    this.originalRolesItems = response.data.items;
                    var size = 7;
                    for (var i=0; i < response.data.items.length; i+=size) {
                      this.rolesItems.push(response.data.items.slice(i,i+size));
                    }
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getUserRolesIds: function (id) {
        this.userRolesManager.getByUserId(id)
                .then( (response) => {
                  if(response.data !== false){
                    this.userRolesIds = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      submit: function () {
        this.$v.$touch();
        if (!this.$v.$invalid || (this.rowId > 0 && this.user_pwd === '' && this.user_name !== '' && this.user_real !== '')) {
          if (this.rowId === 0){
            this.create();
          } else {
            this.update();
          }
        }
      },
      create: function(){

        var userRoleConfig = [];

        this.originalRolesItems.forEach(function (roleItem, index) {
          var currentCheckbox = document.getElementById('role_'+roleItem.id);
          if (currentCheckbox.checked === true) {
            userRoleConfig.push(roleItem.id);
          }
        });

        var createData = {
          user_name: this.user_name,
          user_pwd: this.user_pwd,
          user_real: this.user_real,
          notice: this.notice,
          userRoleConfig: userRoleConfig,
        };

        this.usersManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
                    } else {
                      this.openErrorDialog(response.data.error);
                    }
                  }
                })
                .catch((error) => {
                  console.log(error);
                  this.setDefaultData();
                });
      },
      update: function(){

        var userRoleConfig = [];

        this.originalRolesItems.forEach(function (roleItem, index) {
            var currentCheckbox = document.getElementById('role_'+roleItem.id);
            if (currentCheckbox.checked === true) {
              userRoleConfig.push(roleItem.id);
            }
          });

        var updateData = {
          user_name: this.user_name,
          user_pwd: this.user_pwd,
          user_real: this.user_real,
          notice: this.notice,
          userRoleConfig: userRoleConfig,
          id: this.rowId
        };

        this.usersManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
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
        this.user_name = '';
        this.user_pwd = '';
        this.user_real = '';
        this.notice = '';
        this.userRolesIds = [];
        this.rowId = 0;
      }
    },

    computed: {
      user_nameErrors () {
        const errors = [];
        if (!this.$v.user_name.$dirty) return errors
        !this.$v.user_name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      user_pwdErrors () {
        if (this.rowId > 0) {
          return [];
        }
        const errors = [];
        if (!this.$v.user_pwd.$dirty) return errors
        !this.$v.user_pwd.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      user_realErrors () {
        const errors = [];
        if (!this.$v.user_real.$dirty) return errors
        !this.$v.user_real.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
