<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >



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
  import qs from "qs";

  import {RolesManager} from "../../../managers/RolesManager";

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
        rowId: 0,
        name: '',
        configureItems: []
      }
    },
    props: {
      configureProcessNameTrigger: {type: String, require: false},
    },
    created() {
      this.rolesManager = new RolesManager();
      this.roles = new RolesManager();

      this.$eventHub.$on(this.configureProcessNameTrigger, (data) => {
        this.rolesManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;

                    this.header = this.$store.state.t('Configuring role')+' '+this.name;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });

        this.showDialog = true;
      });

    },

    methods: {

      submit: function () {
        this.$v.$touch();
        if (!this.$v.$invalid) {
            this.update();
        }
      },

      update: function(){

        var updateData = {
          name: this.name,
          description: this.description,
          id: this.rowId
        };

        this.rolesManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    if (!response.data.error){
                      this.$eventHub.$emit(this.configureProcessNameTrigger);
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
        this.configureItems = [];
        this.name = '';
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
