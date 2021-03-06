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
                  v-model="stage"
                  :error-messages="stageErrors"
                  :counter="250"
                  :label="$store.state.t('Stage')"
                  required
                  @input="$v.stage.$touch()"
                  @blur="$v.stage.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="code"
                  :error-messages="codeErrors"
                  :counter="250"
                  :label="$store.state.t('Code')"
                  required
                  @input="$v.code.$touch()"
                  @blur="$v.code.$touch()"
          ></v-text-field>
          <v-select
                  v-model="country_id"
                  :error-messages="country_idErrors"
                  :items="countryItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Country')"
                  required
                  @input="$v.country_id.$touch()"
                  @blur="$v.country_id.$touch()"
          ></v-select>

          <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
          <v-btn  @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../../Layout/Components/DemoCard';

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard
    },

    mixins: [validationMixin],

    validations: {
      stage: { required, maxLength: maxLength(250) },
      code: { required, maxLength: maxLength(250) },
      country_id: { required },
    },

    data () {
      return {
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        stage: '',
        code: '',
        country_id: null,
        countryItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.getCountriesForSelect();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/project-stages/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.stage = response.data.stage;
                    this.code = response.data.code;
                    this.country_id = response.data.country_id;
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
      getCountriesForSelect: function () {
        axios.get(window.apiDomainUrl+'/countries/get-all-for-select', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.countryItems = response.data.items;
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
          stage: this.stage,
          code: this.code,
          country_id: this.country_id
        };

        axios.post(window.apiDomainUrl+'/project-stages/create', qs.stringify(createData))
                .then( (response) => {
                  if (response.data !== false){
                    this.$eventHub.$emit(this.updateItemListNameTrigger);
                    this.showDialog = false;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      },
      update: function(){

        var updateData = {
          stage: this.stage,
          code: this.code,
          country_id: this.country_id,
          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/project-stages/update', qs.stringify(updateData))
                .then( (response) => {
                  if (response.data !== false){
                    this.$eventHub.$emit(this.updateItemListNameTrigger);
                    this.showDialog = false;
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
        this.stage = '';
        this.code = '';
        this.country_id = null;
        this.rowId = 0;
      }
    },

    computed: {
      stageErrors () {
        const errors = []
        if (!this.$v.stage.$dirty) return errors
        !this.$v.stage.maxLength && errors.push(this.$store.state.t('Stage must be at most 250 characters long'))
        !this.$v.stage.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      codeErrors () {
        const errors = []
        if (!this.$v.code.$dirty) return errors
        !this.$v.code.maxLength && errors.push(this.$store.state.t('Code must be at most 250 characters long'))
        !this.$v.code.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      country_idErrors () {
        const errors = []
        if (!this.$v.country_id.$dirty) return errors
        !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
