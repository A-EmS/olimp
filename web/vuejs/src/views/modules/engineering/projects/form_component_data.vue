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
                  v-model="project_stage_id"
                  :error-messages="project_stage_idErrors"
                  :items="projectStagesItems"
                  item-value="id"
                  item-text="stage"
                  :label="$store.state.t('Project Stage')"
                  required
                  @input="$v.project_stage_id.$touch()"
                  @blur="$v.project_stage_id.$touch()"
                  @change="onChangeStage(project_stage_id)"
          ></v-autocomplete>
          <v-autocomplete
                  :disabled="project_stage_id <= 0"
                  v-model="project_part_id"
                  :error-messages="project_part_idErrors"
                  :items="projectPartsItems"
                  item-value="id"
                  item-text="part"
                  :label="$store.state.t('Project Part')"
                  required
                  @input="$v.project_part_id.$touch()"
                  @blur="$v.project_part_id.$touch()"
                  @change="refreshCrypt()"
          ></v-autocomplete>
          <v-text-field
                  v-model="part_crypt"
                  :error-messages="part_cryptErrors"
                  :counter="250"
                  :label="$store.state.t('Part Crypt')"
                  required
                  @input="$v.part_crypt.$touch()"
                  @blur="$v.part_crypt.$touch()"
          ></v-text-field>
          <v-autocomplete
                  v-model="performer_contractor_id"
                  :items="contractor_Items"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Performer')"
          ></v-autocomplete>
          <v-text-field
                  v-model="notice"
                  :label="$store.state.t('Notice')"
                  :counter="250"
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
  import { required, maxLength } from 'vuelidate/lib/validators'
  import qs from "qs";
  import {ProjectStagesManager} from "../../../../managers/ProjectStagesManager";
  import {ProjectPartsManager} from "../../../../managers/ProjectPartsManager";
  import {ProjectDataManager} from "../../../../managers/ProjectDataManager";
  import loadercustom from "../../../components/loadercustom";
  import {CM} from "../../../../managers/ContractorsManager";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      loadercustom
    },

    mixins: [validationMixin],

    validations: {
      part_crypt: { required },
      project_stage_id: { required },
      project_part_id: { required },
    },

    data () {
      return {
        customDialogfrontString: 'Please stand by',
        showCustomLoaderDialog: false,
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,

        part_crypt: '',
        project_stage_id: null,
        project_part_id: null,
        performer_contractor_id: null,
        notice: '',

        projectStagesItems: [],
        projectPartsItems: [],
        contractor_Items: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
      country_id: {type: String, require: true},
      project_id: {type: String, require: true},
    },
    created() {

      this.projectStagesManager = new ProjectStagesManager()
      this.projectPartsManager = new ProjectPartsManager()
      this.projectDataManager = new ProjectDataManager()
      this.contractorManager = new CM();

      this.getContractors();
      this.getProjectStagesByCountry(this.country_id);

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        this.projectDataManager.getById(data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.part_crypt = response.data.part_crypt;
                    this.project_stage_id = response.data.project_stage_id;
                    this.project_part_id = response.data.project_part_id;
                    this.performer_contractor_id = response.data.performer_contractor_id;
                    this.notice = response.data.notice;

                    this.getProjectPartsByStage(response.data.project_stage_id);
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
      getProjectStagesByCountry: function (countryId) {
        this.projectStagesManager.getAllByCountryId(countryId)
                .then( (response) => {
                  if(response.data !== false){
                    this.projectStagesItems = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getProjectPartsByStage: function (project_stage_id) {
        this.projectPartsManager.getAllByStageId(project_stage_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.projectPartsItems = response.data.items;

                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      getContractors: function () {
        this.contractorManager.getForProjectSelect()
                .then( (response) => {
                  if(response.data !== false){
                    this.contractor_Items = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      onChangeStage: function (project_stage_id) {
        this.project_part_id = null;
        this.part_crypt = '';
        this.getProjectPartsByStage(project_stage_id);
      },
      refreshCrypt: function () {
        if (this.project_stage_id <=0 || this.project_id <=0){
          this.part_crypt = '';
          return;
        }

        this.projectDataManager.getRefreshedCrypt(this.project_id, this.project_part_id)
                .then( (response) => {
                  if(response.data !== false){
                    this.part_crypt = response.data;
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
          part_crypt: this.part_crypt,
          project_stage_id: this.project_stage_id,
          project_part_id: this.project_part_id,
          performer_contractor_id: this.performer_contractor_id,
          notice: this.notice,
          project_id: this.project_id
        };

        this.projectDataManager.create(createData)
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
      update: function(){

        var updateData = {
          part_crypt: this.part_crypt,
          project_stage_id: this.project_stage_id,
          project_part_id: this.project_part_id,
          performer_contractor_id: this.performer_contractor_id,
          notice: this.notice,
          project_id: this.project_id,
          id: this.rowId
        };

        this.projectDataManager.update(updateData)
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
      },

      setDefaultData () {
        this.part_crypt = '';
        this.project_stage_id = null;
        this.project_part_id = null;
        this.performer_contractor_id = null;
        this.notice = '';
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
    watch: {
      project_id: function () {
        this.getProjectStagesByCountry(this.country_id);
      }
    },
    computed: {

      project_stage_idErrors () {
        const errors = []
        if (!this.$v.project_stage_id.$dirty) return errors
        !this.$v.project_stage_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      project_part_idErrors () {
        const errors = []
        if (!this.$v.project_part_id.$dirty) return errors
        !this.$v.project_part_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      part_cryptErrors () {
        const errors = []
        if (!this.$v.part_crypt.$dirty) return errors
        !this.$v.part_crypt.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
