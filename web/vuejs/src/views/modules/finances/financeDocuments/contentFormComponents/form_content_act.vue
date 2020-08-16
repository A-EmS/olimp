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
                            v-model="parent_content_id"
                            :error-messages="country_idErrors"
                            :items="countryItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Parent Base Content Line')"
                            required
                            @input="$v.country_id.$touch()"
                            @blur="$v.country_id.$touch()"
                    ></v-select>
                    <v-text-field
                            v-model="percent"
                            :label="$store.state.t('Percent')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                    ></v-text-field>
                    <v-select
                            v-model="service_id"
                            :error-messages="country_idErrors"
                            :items="countryItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Service')"
                            required
                            @input="$v.country_id.$touch()"
                            @blur="$v.country_id.$touch()"
                    ></v-select>
                    <v-select
                            v-model="product_id"
                            :error-messages="country_idErrors"
                            :items="countryItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Product')"
                            required
                            @input="$v.country_id.$touch()"
                            @blur="$v.country_id.$touch()"
                    ></v-select>
                    <v-text-field
                            v-model="amount"
                            :error-messages="amountErrors"
                            :label="$store.state.t('Amount')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                            @input="$v.amount.$touch()"
                            @blur="$v.amount.$touch()"
                            :counter="250"
                    ></v-text-field>
                    <v-text-field
                            v-model="cost_without_tax"
                            :label="$store.state.t('Cost Without Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                    ></v-text-field>
                    <v-text-field
                            v-model="cost_with_tax"
                            :label="$store.state.t('Cost With Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                    ></v-text-field>
                    <v-text-field
                            v-model="summ_without_tax"
                            :label="$store.state.t('Sum Without Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                    ></v-text-field>
                    <v-text-field
                            v-model="summ_tax"
                            :label="$store.state.t('Sum Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                    ></v-text-field>
                    <v-text-field
                            v-model="summ_with_tax"
                            :label="$store.state.t('Sum With Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
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

    import LayoutWrapper from '../../../../../Layout/Components/LayoutWrapper';
    import DemoCard from '../../../../../Layout/Components/DemoCard';

    import { validationMixin } from 'vuelidate'
    import { required, maxLength, email } from 'vuelidate/lib/validators'
    import qs from "qs";
    import customValidationMixin from "../../../../../mixins/customValidationMixin";

    export default {
        components: {
            'layout-wrapper': LayoutWrapper,
            'demo-card': DemoCard,
        },

        mixins: [validationMixin, customValidationMixin],

        validations: {
            name: { required, maxLength: maxLength(250) },
            amount: { required },
            country_id: { required },
        },

        data () {
            return {
                showDialog: false,
                valid: true,
                header: '',
                rowId: 0,
                name: '',
                amount: 0.00,
                percent: 0.00,
                parent_content_id: 0,
                notice: '',
                cost_without_tax: 0.00,
                cost_with_tax: 0.00,
                summ_without_tax: 0.00,
                summ_with_tax: 0.00,
                summ_tax: 0.00,
                service_id: null,
                product_id: null,


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
                axios.get(window.apiDomainUrl+'/regions/get-by-id?id='+data.id, qs.stringify({}))
                    .then( (response) => {
                        if(response.data !== false){
                            this.rowId = response.data.id;
                            this.name = response.data.name;
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
                    name: this.name,
                    country_id: this.country_id
                };

                axios.post(window.apiDomainUrl+'/regions/create', qs.stringify(createData))
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
                    country_id: this.country_id,
                    id: this.rowId
                };

                axios.post(window.apiDomainUrl+'/regions/update', qs.stringify(updateData))
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
                this.country_id = null;
                this.rowId = 0;
            }
        },

        computed: {
            nameErrors () {
                const errors = []
                if (!this.$v.name.$dirty) return errors
                !this.$v.name.maxLength && errors.push(this.$store.state.t('Name must be at most 250 characters long'))
                !this.$v.name.required && errors.push(this.$store.state.t('Required field'))
                return errors
            },
            country_idErrors () {
                const errors = []
                if (!this.$v.country_id.$dirty) return errors
                !this.$v.country_id.required && errors.push(this.$store.state.t('Required field'))
                return errors
            },
            amountErrors () {
                const errors = []
                if (!this.$v.amount.$dirty) return errors
                !this.$v.amount.required && errors.push(this.$store.state.t('Required field'))
                return errors
            },
        },

        beforeDestroy () {
            this.$eventHub.$off(this.createProcessNameTrigger);
            this.$eventHub.$off(this.updateProcessNameTrigger);
        },
    }
</script>
