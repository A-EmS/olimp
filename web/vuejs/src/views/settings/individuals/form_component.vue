<template>
  <div v-if="showDialog">

    <layout-wrapper>

      <demo-card :heading="header" subheading="">

        <v-form
                ref="form"
                v-model="valid"
                lazy-validation
        >

            <b-row>
              <b-col md="12">
                <b-card class="mb-6 nav-justified" no-body>
                  <b-tabs card>
                    <b-tab :title="$store.state.t('User Info')" active>
                          <v-text-field
                                  v-model="third_name"
                                  :error-messages="third_nameErrors"
                                  :counter="255"
                                  :label="$store.state.t('Third Name')"
                                  required
                                  @input="$v.third_name.$touch()"
                                  @blur="$v.third_name.$touch()"
                          ></v-text-field>
                          <v-text-field
                                  v-model="name"
                                  :error-messages="nameErrors"
                                  :counter="255"
                                  :label="$store.state.t('Name')"
                                  required
                                  @input="$v.name.$touch()"
                                  @blur="$v.name.$touch()"
                          ></v-text-field>
                          <v-text-field
                                  v-model="second_name"
                                  :counter="255"
                                  :label="$store.state.t('Second Name')"
                          ></v-text-field>
                          <v-select
                                  v-model="gender"
                                  :error-messages="genderErrors"
                                  :items="[{id:'0', name:$store.state.t('Female')}, {id:'1', name:$store.state.t('Male')}]"
                                  item-value="id"
                                  item-text="name"
                                  :label="$store.state.t('Gender')"
                                  required
                                  @input="$v.gender.$touch()"
                                  @blur="$v.gender.$touch()"
                          ></v-select>

                                <v-menu
                                        ref="birthdayMenu"
                                        v-model="birthdayMenu"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        lazy
                                        transition="scale-transition"
                                        offset-y
                                        full-width
                                        min-width="290px"
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                                v-model="birthday"
                                                :label="$store.state.t('Birthday')"
                                                prepend-icon="event"
                                                readonly
                                                v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                            ref="pickerBirthday"
                                            v-model="birthday"
                                            :locale="$store.state.user.settings.interface_language"
                                            :max="new Date().toISOString().substr(0, 10)"
                                            min="1950-01-01"
                                            @change="changeBirthday"
                                    ></v-date-picker>
                                </v-menu>

                          <v-text-field
                                  v-model="inn"
                                  :counter="255"
                                  :label="$store.state.t('INN')"
                          ></v-text-field>
                    </b-tab>
                    <b-tab :title="$store.state.t('Passport Data')">
                          <v-text-field
                                  v-model="passport_series"
                                  :counter="255"
                                  :label="$store.state.t('Passport Series')"
                          ></v-text-field>

                          <v-text-field
                                  v-model="passport_number"
                                  :counter="255"
                                  :label="$store.state.t('Passport Number')"
                          ></v-text-field>

                          <v-text-field
                                  v-model="passport_authority"
                                  :counter="255"
                                  :label="$store.state.t('Passport Authority')"
                          ></v-text-field>

                        <v-menu
                                ref="authorityDateMenu"
                                v-model="authorityDateMenu"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                                <v-text-field
                                        v-model="passport_authority_date"
                                        :label="$store.state.t('Passport Authority Date')"
                                        prepend-icon="event"
                                        readonly
                                        v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                    ref="pickerAuthorityDate"
                                    v-model="passport_authority_date"
                                    :locale="$store.state.user.settings.interface_language"
                                    :max="new Date().toISOString().substr(0, 10)"
                                    min="1950-01-01"
                                    @change="changeAuthorityDate"
                            ></v-date-picker>
                        </v-menu>

                          <v-text-field
                                  v-model="notice"
                                  :label="$store.state.t('Notice')"
                          ></v-text-field>
                    </b-tab>
                  </b-tabs>
                </b-card>
              </b-col>
            </b-row>

            <br />
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
      name: { required, maxLength: maxLength(255) },
      third_name: { required, maxLength: maxLength(255) },
      gender: { required, maxLength: maxLength(255) },
    },
    watch: {
        birthdayMenu (val) {
            val && setTimeout(() => (this.$refs.pickerBirthday.activePicker = 'YEAR'))
        },
        authorityDateMenu (val) {
            val && setTimeout(() => (this.$refs.pickerAuthorityDate.activePicker = 'YEAR'))
        }
    },
    data () {
      return {
        showDialog: false,
        valid: true,
        header: 'Action...',
        rowId: 0,
        name: '',
        second_name: '',
        third_name: '',
        gender: '',
        birthday: '',
        inn: '',
        passport_series: '',
        passport_number: '',
        passport_authority: '',
        passport_authority_date: '',
        notice: '',

        birthdayMenu: false,
        authorityDateMenu: false,

      }
    },
    props: {
      createProcessNameTrigger: {type: String, require: false},
      updateProcessNameTrigger: {type: String, require: false},
      updateItemListNameTrigger: {type: String, require: false},
    },
    created() {

      this.$eventHub.$on('olol',function (date) {
        console.log(date);
      });

      this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
        this.header = this.$store.state.t('Creating new')+'...';
        this.setDefaultData();
        this.showDialog = true;
      });

      this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
        axios.get(window.apiDomainUrl+'/individuals/get-by-id?id='+data.id, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.rowId = response.data.id;
                    this.name = response.data.name;
                    this.second_name = response.data.second_name;
                    this.third_name = response.data.third_name;
                    this.gender = response.data.gender;
                    this.birthday = response.data.birthday;
                    this.inn = response.data.inn;
                    this.passport_series = response.data.passport_series;
                    this.passport_number = response.data.passport_number;
                    this.passport_authority = response.data.passport_authority;
                    this.passport_authority_date = response.data.passport_authority_date;
                    this.notice = response.data.notice;
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
        changeBirthday (date) {
            this.$refs.birthdayMenu.save(date)
        },
        changeAuthorityDate (date) {
            this.$refs.authorityDateMenu.save(date)
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
          second_name: this.second_name,
          third_name: this.third_name,
          gender: this.gender,
          birthday: this.birthday,
          inn: this.inn,
          passport_series: this.passport_series,
          passport_number: this.passport_number,
          passport_authority: this.passport_authority,
          passport_authority_date: this.passport_authority_date,
          notice: this.notice
        };

        axios.post(window.apiDomainUrl+'/individuals/create', qs.stringify(createData))
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
          second_name: this.second_name,
          third_name: this.third_name,
          gender: this.gender,
          birthday: this.birthday,
          inn: this.inn,
          passport_series: this.passport_series,
          passport_number: this.passport_number,
          passport_authority: this.passport_authority,
          passport_authority_date: this.passport_authority_date,
          notice: this.notice,
          id: this.rowId
        };

        axios.post(window.apiDomainUrl+'/individuals/update', qs.stringify(updateData))
                .then( (response) => {
                  if (response.data !== false){
                    this.$eventHub.$emit(this.updateItemListNameTrigger);
                    this.showDialog = false;
                    // window.location.reload();
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
        this.second_name = '';
        this.third_name = '';
        this.gender = '';
        this.birthday = '';
        this.inn = '';
        this.passport_series = '';
        this.passport_number = '';
        this.passport_authority = '';
        this.passport_authority_date = '';
        this.notice = '';
        this.rowId = 0;
      }
    },

    computed: {
      nameErrors () {
        const errors = []
        if (!this.$v.name.$dirty) return errors
        !this.$v.name.maxLength && errors.push(this.$store.state.t('Name must be at most 255 characters long'))
        !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      third_nameErrors () {
        const errors = []
        if (!this.$v.third_name.$dirty) return errors
        !this.$v.third_name.maxLength && errors.push(this.$store.state.t('Third Name must be at most 255 characters long'))
        !this.$v.third_name.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
      genderErrors () {
        const errors = []
        if (!this.$v.gender.$dirty) return errors
        !this.$v.gender.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.createProcessNameTrigger);
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
