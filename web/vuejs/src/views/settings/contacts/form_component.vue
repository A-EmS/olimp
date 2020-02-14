<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >
          <v-text-field
                  v-model="name"
                  :error-messages="nameErrors"
                  :counter="250"
                  :label="$store.state.t('Contact')"
                  required
                  @input="$v.name.$touch()"
                  @blur="$v.name.$touch()"
          ></v-text-field>

          <v-select
                  v-model="contact_type_id"
                  :error-messages="contact_type_idErrors"
                  :items="contact_typesItems"
                  item-value="id"
                  item-text="contact_type"
                  :label="$store.state.t('Entity Type')"
                  required
                  @input="$v.contact_type_id.$touch()"
                  @blur="$v.contact_type_id.$touch()"
          ></v-select>
          <v-select v-show="contractorShow"
                  v-model="contractor_id"
                  :error-messages="contractor_idErrors"
                  :items="contractor_Items"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Contractor')"
                  required
                  @input="$v.contractor_id.$touch()"
                  @blur="$v.contractor_id.$touch()"
          ></v-select>


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
    <confirmator
            :handlerInputProcessName.sync="confirmatorInputProcessName"
            :handlerOutputProcessName.sync="confirmatorOutputProcessName">
    </confirmator>
    <confirmator
            :handlerInputProcessName.sync="confirmatorInputProcessUpdateName"
            :handlerOutputProcessName.sync="confirmatorOutputProcessUpdateName">
    </confirmator>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';
  import flag from "../../components/flag";
  import {CM} from "../../../managers/ContractorsManager";
  import confirmator from "../../components/confirmator";
  import loadercustom from "../../components/loadercustom";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      flag,
      CM,
      confirmator,
      loadercustom
    },

    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(250) },
      contact_type_id: { required },
      contractor_id: { required },
    },

    data () {
      return {
        contractorManager:null,

        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',

        confirmatorInputProcessName: 'confirm:forceContact',
        confirmatorOutputProcessName: 'confirmed:forceContact',
        confirmatorInputProcessUpdateName: 'confirm:forceUpdateContact',
        confirmatorOutputProcessUpdateName: 'confirmed:forceUpdateContact',
        forceSaveUpdate: false,

        showDialog: false,
        contractorShow: true,
        valid: true,
        header: '',
        rowId: 0,
        name: '',
        notice: '',
        contact_type_id: null,
        contact_typesItems: [],
        contractor_id: null,
        contractor_Items: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {
      this.contractorManager = new CM();
      this.getContactTypes();
      this.getContractors();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;

        if (typeof data.refId !== 'undefined' && data.refId > 0){
          this.getContractorByRefIdAndType({ref_id:data.refId, is_entity: data.isEntity})
        }

      });

      this.$eventHub.$on(this.confirmatorOutputProcessName, (data) => {
        this.forceSaveUpdate = true;
        this.create();
      });

      this.$eventHub.$on(this.confirmatorOutputProcessUpdateName, (data) => {
        this.forceSaveUpdate = true;
        this.update();
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/contacts/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.notice = response.data.notice;
                    this.contact_type_id = response.data.contact_type_id;
                    this.contractor_id = response.data.contractor_id;

                    if (data.notOriginalPage === true) {
                      this.contractorShow = false;
                    }
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
      getContactTypes: function () {
        axios.get(window.apiDomainUrl+'/contact-types/get-all-for-select', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.contact_typesItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getContractors: function () {
        this.contractorManager.getForSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.contractor_Items = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getContractorByRefIdAndType: function (data) {
        this.contractorManager.getContractorByRefIdAndType(data)
                .then( (response) => {
                  if(response.data !== false){
                    this.contractor_id = response.data.item.id;
                    this.contractorShow = false;
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
          name: this.name,
          notice: this.notice,
          contact_type_id: this.contact_type_id,
          contractor_id: this.contractor_id,
          force_action: this.forceSaveUpdate,
        };

        axios.post(window.apiDomainUrl+'/contacts/create', qs.stringify(createData))
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                        this.$eventHub.$emit(this.updateItemListNameTrigger);
                        this.showDialog = false;
                        this.setDefaultData();
                    } else {
                        if (response.data.duplicate){
                            this.$eventHub.$emit(this.confirmatorInputProcessName, {
                                titleString: this.$store.state.t('Force Creating Contact') + '...',
                                confirmString: this.$store.state.t(response.data.error)
                            });
                        } else {
                          this.openErrorDialog(response.data.error);
                        }
                    }
                  }
                })
                .catch( (error) => {
                  this.setDefaultData();
                  this.openErrorDialog(error);
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          name: this.name,
          notice: this.notice,
          contact_type_id: this.contact_type_id,
          contractor_id: this.contractor_id,
          id: this.rowId,
          force_action: this.forceSaveUpdate,
        };

        axios.post(window.apiDomainUrl+'/contacts/update', qs.stringify(updateData))
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                        this.$eventHub.$emit(this.updateItemListNameTrigger);
                        this.showDialog = false;
                        this.setDefaultData();
                    } else {
                        if (response.data.duplicate){
                            this.$eventHub.$emit(this.confirmatorInputProcessUpdateName, {
                                titleString: this.$store.state.t('Force Updating Contact') + '...',
                                confirmString: this.$store.state.t(response.data.error)
                            });
                        } else {
                          this.openErrorDialog(response.data.error);
                        }
                    }
                  }
                })
                .catch((error) => {
                  this.setDefaultData();
                  this.openErrorDialog(error);
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

      setDefaultData () {
        this.name = '';
        this.notice = '';
        this.contact_type_id = null;
        this.contractor_id = null;
        this.forceSaveUpdate = false;
        this.rowId = 0;
      },

      openErrorDialog(message, time){
        var dialogTime = time || 5000;
        this.customDialogfrontString = this.$store.state.t(message);
        this.showCustomLoaderDialog = true;
        setTimeout(() => {
          this.showCustomLoaderDialog = false;
        }, dialogTime);
      },
    },

    computed: {
      nameErrors () {
        const errors = [];
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push(this.$store.state.t('Name must be at most 250 characters long'))
        !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      contact_type_idErrors () {
        const errors = [];
        if (!this.$v.contact_type_id.$dirty) return errors
        !this.$v.contact_type_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      contractor_idErrors () {
        const errors = [];
        if (!this.$v.contractor_id.$dirty) return errors
        !this.$v.contractor_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
      this.$eventHub.$off(this.confirmatorOutputProcessName);
      this.$eventHub.$off(this.confirmatorInputProcessUpdateName);
    },
  }
</script>
