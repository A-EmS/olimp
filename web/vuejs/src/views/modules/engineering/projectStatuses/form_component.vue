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
                  v-model="status_en"
                  :error-messages="statusEnErrors"
                  :counter="250"
                  :label="$store.state.t('Status EN')"
                  required
                  @input="$v.status_en.$touch()"
                  @blur="$v.status_en.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="status_ru"
                  :counter="250"
                  :label="$store.state.t('Status RU')"
          ></v-text-field>

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

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";
  import {ProjectStatusesManager} from "../../../../managers/ProjectStatusesManager";
  import {CountriesManager} from "../../../../managers/CountriesManager";
  import loadercustom from "../../../components/loadercustom";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom,
    },

    mixins: [validationMixin],

    validations: {
      status_en: { required, maxLength: maxLength(250) },
    },

    data () {
      return {
        showDialog: false,
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',
        valid: true,
        header: '',
        rowId: 0,

        status_en: '',
        status_ru: '',
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.projectStatusesManager = new ProjectStatusesManager();
      this.countriesManager = new CountriesManager();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.projectStatusesManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.status_en = response.data.status_en;
                    this.status_ru = response.data.status_ru;
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
          status_en: this.status_en,
          status_ru: this.status_ru
        };

        this.projectStatusesManager.create(createData)
                .then( (response) => {
                  if (response.data !== false){
                    if (response.data.error) {
                      this.showCustomLoaderDialog = true;
                      this.customDialogfrontString = this.$store.state.t(response.data.error);
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, 3000);
                    } else {
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
                    }
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          status_en: this.status_en,
          status_ru: this.status_ru,
          id: this.rowId
        };

        this.projectStatusesManager.update(updateData)
                .then( (response) => {
                  if (response.data !== false){
                    if (response.data.error) {
                      this.showCustomLoaderDialog = true;
                      this.customDialogfrontString = this.$store.state.t(response.data.error);
                      setTimeout(() => {
                        this.showCustomLoaderDialog = false;
                      }, 3000);
                    } else {
                      this.$eventHub.$emit(this.updateItemListNameTrigger);
                      this.showDialog = false;
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
      },

      setDefaultData () {
        this.status_en = '';
        this.status_ru = '';
        this.rowId = 0;
      }
    },

    computed: {
      statusEnErrors () {
        const errors = []
        if (!this.$v.status_en.$dirty) return errors
        !this.$v.status_en.maxLength && errors.push(this.$store.state.t('Status must be at most 250 characters long'))
        !this.$v.status_en.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
