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
                  v-model="lang_en"
                  :error-messages="lang_enErrors"
                  :counter="1000"
                  label="lang_en"
                  required
                  @input="$v.lang_en.$touch()"
                  @blur="$v.lang_en.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="lang_ru"
                  :error-messages="lang_ruErrors"
                  :counter="1000"
                  label="lang_ru"
                  required
                  @input="$v.lang_ru.$touch()"
                  @blur="$v.lang_ru.$touch()"
          ></v-text-field>
          <v-btn color="success" @click="submit">submit</v-btn>
          <v-btn  @click="cancel">cancel</v-btn>
        </v-form>
      </demo-card>

    </layout-wrapper>
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';

  import { validationMixin } from 'vuelidate'
  import { required, maxLength } from 'vuelidate/lib/validators'
  import qs from "qs";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
    },

    mixins: [validationMixin],

    validations: {
      lang_en: { required, maxLength: maxLength(250) },
      lang_ru: { required, maxLength: maxLength(250) },
    },

    data () {
      return {
        showDialog: false,
        valid: true,
        header: 'Action...',
        rowId: 0,

        lang_en: '',
        lang_ru: '',
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = 'Creating new...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/interface-vocabularies/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.lang_en = response.data.lang_en;
                    this.lang_ru = response.data.lang_ru;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
        this.header = 'Updating...';
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
          lang_en: this.lang_en,
          lang_ru: this.lang_ru,
        };

        axios.post(window.apiDomainUrl+'/interface-vocabularies/create', qs.stringify(createData))
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
          lang_en: this.lang_en,
          lang_ru: this.lang_ru,

          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/interface-vocabularies/update', qs.stringify(updateData))
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
        this.lang_en = '';
        this.lang_ru = '';
      }
    },

    computed: {
      lang_enErrors () {
        const errors = []
        if (!this.$v.lang_en.$dirty) return errors
        !this.$v.lang_en.maxLength && errors.push('Lang En must be at most 1000 characters long')
        !this.$v.lang_en.required && errors.push('Lang EN is required.')
        return errors
      },
      lang_ruErrors () {
        const errors = []
        if (!this.$v.lang_ru.$dirty) return errors
        !this.$v.lang_ru.maxLength && errors.push('Lang RU must be at most 1000 characters long')
        !this.$v.lang_ru.required && errors.push('Lang RU is required.')
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
