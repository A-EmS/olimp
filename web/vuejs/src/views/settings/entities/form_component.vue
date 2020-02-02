<template>
  <div v-if="showDialog">
    <layout-wrapper>

      <demo-card :heading="header" subheading="">
        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >
          <v-select
                  v-model="entity_type_id"
                  :error-messages="entity_type_idErrors"
                  :items="entity_typesItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Entity Types')"
                  required
                  @input="$v.entity_type_id.$touch()"
                  @blur="$v.entity_type_id.$touch()"
          ></v-select>

          <v-text-field
                  v-model="name"
                  :error-messages="nameErrors"
                  :counter="250"
                  :label="$store.state.t('Name')"
                  required
                  @input="$v.name.$touch()"
                  @blur="$v.name.$touch()"
          ></v-text-field>
          <v-text-field
                  v-model="short_name"
                  :error-messages="short_nameErrors"
                  :counter="250"
                  :label="$store.state.t('Short Name')"
                  required
                  @input="$v.short_name.$touch()"
                  @blur="$v.short_name.$touch()"
          ></v-text-field>

          <v-text-field
                  v-model="ogrn"
                  :counter="250"
                  :label="$store.state.t('OGRN')"
          ></v-text-field>
          <v-text-field
                  v-model="inn"
                  :counter="250"
                  :label="$store.state.t('INN')"
          ></v-text-field>
          <v-text-field
                  v-model="kpp"
                  :counter="250"
                  :label="$store.state.t('KPP')"
          ></v-text-field>
          <v-text-field
                  v-model="okpo"
                  :counter="250"
                  :label="$store.state.t('OKPO')"
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
  </div>
</template>

<script>

  import LayoutWrapper from '../../../Layout/Components/LayoutWrapper';
  import DemoCard from '../../../Layout/Components/DemoCard';
  import flag from "../../components/flag";

  import { validationMixin } from 'vuelidate'
  import { required, maxLength, email } from 'vuelidate/lib/validators'
  import qs from "qs";

  export default {
    components: {
      'layout-wrapper': LayoutWrapper,
      'demo-card': DemoCard,
      flag
    },

    mixins: [validationMixin],

    validations: {
      name: { required, maxLength: maxLength(250) },
      short_name: { required, maxLength: maxLength(250) },
      entity_type_id: { required },
    },

    data () {
      return {
        showDialog: false,
        valid: true,
        header: '',
        rowId: 0,
        name: '',
        short_name: '',
        ogrn: '',
        inn: '',
        kpp: '',
        okpo: '',
        notice: '',
        entity_type_id: null,
        entity_typesItems: [],
      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.getEntityTypes();

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/entities/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.short_name = response.data.short_name;
                    this.notice = response.data.notice;
                    this.entity_type_id = response.data.entity_type_id;
                    this.ogrn = response.data.ogrn;
                    this.inn = response.data.inn;
                    this.kpp = response.data.kpp;
                    this.okpo = response.data.okpo;
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
      getEntityTypes: function () {
        axios.get(window.apiDomainUrl+'/entity-types/get-all-for-select', qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.entity_typesItems = response.data.items;
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
          short_name: this.short_name,
          notice: this.notice,
          entity_type_id: this.entity_type_id,
          ogrn: this.ogrn,
          inn: this.inn,
          kpp: this.kpp,
          okpo: this.okpo
        };

        axios.post(window.apiDomainUrl+'/entities/create', qs.stringify(createData))
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
          name: this.name,
          short_name: this.short_name,
          notice: this.notice,
          entity_type_id: this.entity_type_id,
          ogrn: this.ogrn,
          inn: this.inn,
          kpp: this.kpp,
          okpo: this.okpo,

          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/entities/update', qs.stringify(updateData))
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
        this.name = '';
        this.short_name = '';
        this.ogrn = '';
        this.inn = '';
        this.kpp = '';
        this.okpo = '';
        this.notice = '';
        this.entity_type_id = null;

        this.rowId = 0;
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
      short_nameErrors () {
        const errors = [];
        if (!this.$v.short_name.$dirty) return errors
        !this.$v.short_name.maxLength && errors.push(this.$store.state.t('Short Name must be at most 250 characters long'))
        !this.$v.short_name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      entity_type_idErrors () {
        const errors = [];
        if (!this.$v.entity_type_id.$dirty) return errors
        !this.$v.entity_type_id.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
