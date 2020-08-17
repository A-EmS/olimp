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
                  v-show="!entity_settled_id"
                  v-model="entity_id"
                  :error-messages="entity_idErrors"
                  :items="entityItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Entities')"
                  required
                  @input="$v.entity_id.$touch()"
                  @blur="$v.entity_id.$touch()"
          ></v-autocomplete>
          <v-autocomplete
                  v-show="!individual_settled_id"
                  v-model="individual_id"
                  :error-messages="individual_idErrors"
                  :items="individualItems"
                  item-value="id"
                  item-text="nameWithId"
                  :label="$store.state.t('Individuals')"
                  required
                  @input="$v.individual_id.$touch()"
                  @blur="$v.individual_id.$touch()"
          ></v-autocomplete>

          <v-text-field
                  v-model="position"
                  :error-messages="positionErrors"
                  :counter="250"
                  :label="$store.state.t('Position')"
                  required
                  @input="$v.position.$touch()"
                  @blur="$v.position.$touch()"
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
  import flag from "../../components/flag";
  import loadercustom from "../../components/loadercustom";


  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";

  import {EM} from "../../../managers/EntitiesManager";
  import {IM} from "../../../managers/IndividualsManager";
  import {PM} from "../../../managers/PersonalManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      flag,
      EM,
      IM,
      PM,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {
      position: { required, maxLength: maxLength(250) },
      entity_id: { required },
      individual_id: { required },
    },

    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',

        individualsManager:null,
        entitiesManager:null,
        personalManager:null,

        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        position: '',
        notice: '',
        entity_id: null,
        entityItems: [],
        individual_id: null,
        individualItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
      entity_settled_id: {type: Number, require: false},
      individual_settled_id: {type: Number, require: false},
    },
    created() {
      this.entitiesManager = new EM();
      this.individualsManager = new IM();
      this.personalManager = new PM();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.initFormComponent();
        this.personalManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.position = response.data.position;
                    this.notice = response.data.notice;
                    this.entity_id = response.data.entity_id;
                    this.individual_id = response.data.individual_id;
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
      initFormComponent: function(){
        this.getEntities();
        this.getIndividuals();
      },
      getEntities: function () {
        this.entitiesManager.getForSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.entityItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getIndividuals: function () {
        this.individualsManager.getForSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.individualItems = response.data.items;
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
          position: this.position,
          notice: this.notice,
          entity_id: this.entity_id,
          individual_id: this.individual_id,
        };

        this.personalManager.create(createData)
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

        var updateData = {
          position: this.position,
          notice: this.notice,
          entity_id: this.entity_id,
          individual_id: this.individual_id,
          id: this.rowId
        };

        this.personalManager.update(updateData)
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
        this.position = '';
        this.notice = '';
        this.entity_id = this.entity_settled_id || null;
        this.individual_id = this.individual_settled_id || null;
        this.rowId = 0;
      }
    },

    computed: {
      positionErrors () {
        const errors = [];
        if (!this.$v.position.$dirty) return errors
        !this.$v.position.maxLength && errors.push(this.$store.state.t('Position must be at most 250 characters long'))
        !this.$v.position.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      entity_idErrors () {
        const errors = [];
        if (!this.$v.entity_id.$dirty) return errors
        !this.$v.entity_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      individual_idErrors () {
        const errors = [];
        if (!this.$v.individual_id.$dirty) return errors
        !this.$v.individual_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
