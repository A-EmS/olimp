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
                            v-model="service_id"
                            :error-messages="serviceErrors"
                            :items="serviceItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Service')"
                            @change="onChangeService"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-model="product_id"
                            :error-messages="productErrors"
                            :items="productItems"
                            item-value="id"
                            item-text="name"
                            :label="$store.state.t('Product')"
                            @change="onChangeProduct"
                    ></v-autocomplete>
                    <v-flex xs12 sm12 md12>
                        <v-menu
                                v-model="start_date_menu"
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
                                        v-model="start_date"
                                        :label="$store.state.t('Start Date')"
                                        prepend-icon="event"
                                        required
                                        v-on="on"
                                        @change="onChangeStartDate"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="start_date" @input="start_date_menu = false" @change="onChangeStartDate"></v-date-picker>
                        </v-menu>
                    </v-flex>
                    <v-container fluid>
                        <v-layout row wrap>

                            <v-flex xs12 sm2>
                                <v-select
                                        v-model="period_type"
                                        :items="periodTypeItems"
                                        item-value="id"
                                        item-text="name"
                                        :label="$store.state.t('Period Type')"
                                        @change="onChangePeriodType"
                                ></v-select>
                            </v-flex>

                            <v-flex xs12 sm10>
                                <v-text-field
                                        v-model="period_amount"
                                        :label="$store.state.t('Period Amount')"
                                        type="number"
                                        step="1"
                                        @change="onChangePeriodAmount"
                                        @keyup="onChangePeriodAmount"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-flex xs12 sm12 md12>
                        <v-menu
                                v-model="end_date_menu"
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
                                        v-model="end_date"
                                        :label="$store.state.t('End Date')"
                                        prepend-icon="event"
                                        required
                                        v-on="on"
                                        @change="onChangeEndDate"
                                ></v-text-field>
                            </template>
                            <v-date-picker v-model="end_date" @input="end_date_menu = false" @change="onChangeEndDate"></v-date-picker>
                        </v-menu>
                    </v-flex>
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
                            @keyup="calculateSums()"
                    ></v-text-field>
                    <div v-if="taxesRates.name" class="alert alert-warning">{{$store.state.t("Tax Rate According Document's Own Company")}}: {{$store.state.t(taxesRates.name)}}</div>
                    <v-text-field
                            v-model="cost_without_tax"
                            :error-messages="cost_without_taxErrors"
                            :label="$store.state.t('Cost Without Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                            @input="$v.cost_without_tax.$touch()"
                            @blur="$v.cost_without_tax.$touch()"
                            @keyup="onChangeCostWithoutTax()"
                            @change="onChangeCostWithoutTax()"
                    ></v-text-field>
                    <v-text-field
                            v-model="cost_with_tax"
                            :error-messages="cost_with_taxErrors"
                            :label="$store.state.t('Cost With Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                            @input="$v.cost_with_tax.$touch()"
                            @blur="$v.cost_with_tax.$touch()"
                            @keyup="onChangeCostWithTax()"
                            @change="onChangeCostWithTax()"
                    ></v-text-field>
                    <v-text-field
                            v-model="summ_without_tax"
                            :error-messages="summ_without_taxErrors"
                            :label="$store.state.t('Sum Without Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                            @input="$v.summ_without_tax.$touch()"
                            @blur="$v.summ_without_tax.$touch()"
                    ></v-text-field>
                    <v-text-field
                            v-model="summ_tax"
                            :error-messages="summ_taxErrors"
                            :label="$store.state.t('Sum Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                            @input="$v.summ_tax.$touch()"
                            @blur="$v.summ_tax.$touch()"
                    ></v-text-field>
                    <v-text-field
                            v-model="summ_with_tax"
                            :error-messages="summ_with_taxErrors"
                            :label="$store.state.t('Sum With Tax')"
                            required
                            type="number"
                            min="0"
                            step="0.01"
                            @input="$v.summ_with_tax.$touch()"
                            @blur="$v.summ_with_tax.$touch()"
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

    import LayoutWrapper from '../../../../../Layout/Components/LayoutWrapper';
    import DemoCard from '../../../../../Layout/Components/DemoCard';

    import loadercustom from "../../../../components/loadercustom";
    import { validationMixin } from 'vuelidate'
    import { required, maxLength, email, minValue } from 'vuelidate/lib/validators'
    import qs from "qs";
    import customValidationMixin from "../../../../../mixins/customValidationMixin";
    import {ServicesManager} from "../../../../../managers/ServicesManager";
    import {ProductsManager} from "../../../../../managers/ProductsManager";
    import {FinanceDocumentsContentManager} from "../../../../../managers/FinanceDocumentsContentManager";
    import {TaxesManager} from "../../../../../managers/TaxesManager";
    import constantsMixin from "../../../../../mixins/constantsMixin";
    import mathMixin from "../../../../../mixins/mathMixin";
    import {PeriodManager} from "../../../../../managers/PeriodManager";
    import {FinanceDocumentsManager} from "../../../../../managers/FinanceDocumentsManager";
    import {CalendarManager} from "../../../../../managers/CalendarManager";

    export default {
        components: {
            'layout-wrapper': LayoutWrapper,
            'demo-card': DemoCard,
            loadercustom
        },

        mixins: [validationMixin, customValidationMixin, constantsMixin, mathMixin],

        validations: {
            amount: { required, minValue: minValue(0.01) },
            cost_without_tax: { required, minValue: minValue(0.01) },
            cost_with_tax: { required, minValue: minValue(0.01) },
            summ_without_tax: { required, minValue: minValue(0.01) },
            summ_with_tax: { required, minValue: minValue(0.01) },
            summ_tax: { required },
        },

        data () {
            return {
                customDialogfrontString: 'Please stand by',
                showCustomLoaderDialog: false,
                showDialog: false,
                valid: true,
                header: '',
                rowId: 0,
                amount: 0.00,
                notice: '',
                cost_without_tax: 0.00,
                cost_with_tax: 0.00,
                summ_without_tax: 0.00,
                summ_with_tax: 0.00,
                summ_tax: 0.00,

                period_type: 0,
                periodTypeItems: [],
                period_amount: 0,
                start_date_menu: false,
                start_date: null,
                end_date_menu: false,
                end_date: null,
                country_id: 0,

                taxesRates: {},

                service_id: null,
                serviceItems: [],

                product_id: null,
                productItems: [],
            }
        },
        props: {
            document_id: {type: Number, require: false, default: 0},
            createProcessNameTrigger: {type: String, require: false},
            updateProcessNameTrigger: {type: String, require: false},
            updateItemListNameTrigger: {type: String, require: false},
        },
        created() {

            this.calendarManager = new CalendarManager();
            this.periodManager = new PeriodManager();
            this.taxesManager = new TaxesManager();
            this.servicesManager = new ServicesManager();
            this.productsManager = new ProductsManager();
            this.financeDocumentsContentManager = new FinanceDocumentsContentManager();
            this.financeDocumentsManager = new FinanceDocumentsManager();

            this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
                this.header = this.$store.state.t('Creating new')+'...';
                this.initFormComponent();
                this.setDefaultData();
                this.getStartDate();
                this.showDialog = true;
            });

            this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
                this.initFormComponent();
                this.financeDocumentsContentManager.get(data.id)
                    .then( (response) => {
                        if(response.data !== false){
                            this.rowId = response.data.id;
                            // this.document_id = response.data.document_id;
                            this.amount = response.data.amount;
                            this.notice = response.data.notice;
                            this.cost_without_tax = response.data.cost_without_tax;
                            this.cost_with_tax = response.data.cost_with_tax;
                            this.summ_with_tax = response.data.summ_with_tax;
                            this.summ_tax = response.data.summ_tax;
                            this.service_id = response.data.service_id;
                            this.product_id = response.data.product_id;
                            this.summ_without_tax = response.data.summ_without_tax;
                            this.period_amount = response.data.period_amount;
                            this.period_type = parseInt(response.data.period_type);
                            this.start_date = response.data.start_date;
                            this.end_date = response.data.end_date;
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
                this.getServices();
                this.getProducts();
                this.getTaxesRates();
                this.getPeriodTypes();
                this.getCountry();
            },
            onChangePeriodType: function(){
                this.recalculateEndDate();
            },
            onChangePeriodAmount: function(){
                this.recalculateEndDate();
            },
            onChangeEndDate: function(){
                this.period_type = 1;
                var startDatePeriod = new Date(this.start_date);
                var endDatePeriod = new Date(this.end_date);
                var timeDiff = endDatePeriod.getTime() - startDatePeriod.getTime();
                this.period_amount = Math.ceil(timeDiff / (1000 * 3600 * 24));
            },
            onChangeStartDate: function(){
                this.end_date = null;
                this.period_amount = 0;
                this.period_type = 0;
            },
            recalculateEndDate: function(){
                this.calendarManager.calculateDate({startDate: this.start_date, periodAmount: this.period_amount, periodType: this.period_type, countryId: this.country_id})
                    .then( (response) => {
                        if(response.data !== false){
                            this.end_date = response.data.item.date;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getStartDate: function(){
                this.financeDocumentsManager.getRowById(this.document_id)
                    .then( (response) => {
                        if(response.data !== false){
                            this.start_date = response.data.date;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getCountry: function(){
                this.financeDocumentsManager.getRowById(this.document_id)
                    .then( (response) => {
                        if(response.data !== false){
                            this.country_id = response.data.country_id;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getPeriodTypes: function(){
                this.periodManager.getPeriodTypes()
                    .then( (response) => {
                        if(response.data !== false){
                            this.periodTypeItems = response.data;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            onChangeCostWithoutTax: function(){
                var tax_part = parseFloat(parseFloat(this.taxesRates.tax_part).toFixed(4));
                this.cost_without_tax = parseFloat(parseFloat(this.cost_without_tax).toFixed(4));
                if ([this.constants.tax_afterOperations5_Id, this.constants.tax_afterOperations6_Id].includes(this.taxesRates.id)){
                    this.cost_with_tax = this.cost_without_tax / (1 - tax_part);
                } else if ([this.constants.tax_beforeOperations18_Id, this.constants.tax_beforeOperations20_Id].includes(this.taxesRates.id)) {
                    this.cost_with_tax = this.cost_without_tax * (1 + tax_part);
                }

                this.cost_with_tax = this.round(this.cost_with_tax, 4);

                this.calculateSums();
            },
            onChangeCostWithTax: function(){
                var tax_part = parseFloat(parseFloat(this.taxesRates.tax_part).toFixed(4));
                this.cost_with_tax =  parseFloat(parseFloat(this.cost_with_tax).toFixed(4));
                if ([this.constants.tax_afterOperations5_Id, this.constants.tax_afterOperations6_Id].includes(this.taxesRates.id)){
                    this.cost_without_tax = this.cost_with_tax * (1 - tax_part);
                } else if ([this.constants.tax_beforeOperations18_Id, this.constants.tax_beforeOperations20_Id].includes(this.taxesRates.id)) {
                    this.cost_without_tax = this.cost_with_tax / (1 + tax_part);
                }

                this.cost_without_tax = this.round(this.cost_without_tax, 4);

                this.calculateSums();
            },
            calculateSums: function(){
                this.summ_without_tax = this.amount * this.cost_without_tax;
                this.summ_with_tax = this.amount * this.cost_with_tax;
                this.summ_tax = this.round((this.summ_with_tax - this.summ_without_tax), 4);
            },
            getTaxesRates: function(){
                this.taxesManager.getForDocumentContent(this.document_id)
                    .then( (response) => {
                        if(response.data !== false){
                            this.taxesRates = response.data;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            onChangeService: function(){
                this.product_id = null;
            },
            onChangeProduct: function(){
                this.service_id = null;
            },
            getServices: function () {
                this.servicesManager.getAll()
                    .then( (response) => {
                        if(response.data !== false){
                            this.serviceItems = response.data.items;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getProducts: function () {
                this.productsManager.getAll()
                    .then( (response) => {
                        if(response.data !== false){
                            this.productItems = response.data.items;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            submit: function () {
                this.$v.$touch();
                if (!this.$v.$invalid && (this.service_id != null || this.product_id != null)) {
                    if (this.rowId === 0){
                        this.create();
                    } else {
                        this.update();
                    }
                }
            },
            create: function(){

                var createData = {
                    amount: this.amount,
                    notice: this.notice,
                    cost_without_tax: this.cost_without_tax,
                    cost_with_tax: this.cost_with_tax,
                    summ_with_tax: this.summ_with_tax,
                    summ_tax: this.summ_tax,
                    service_id: this.service_id,
                    product_id: this.product_id,
                    summ_without_tax: this.summ_without_tax,
                    document_id: this.document_id,
                    period_type: this.period_type,
                    period_amount: this.period_amount,
                    start_date: this.start_date,
                    end_date: this.end_date,
                };

                this.financeDocumentsContentManager.create(createData)
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
                    amount: this.amount,
                    notice: this.notice,
                    cost_without_tax: this.cost_without_tax,
                    cost_with_tax: this.cost_with_tax,
                    summ_with_tax: this.summ_with_tax,
                    summ_tax: this.summ_tax,
                    service_id: this.service_id,
                    product_id: this.product_id,
                    summ_without_tax: this.summ_without_tax,
                    document_id: this.document_id,
                    period_type: this.period_type,
                    period_amount: this.period_amount,
                    start_date: this.start_date,
                    end_date: this.end_date,
                    id: this.rowId
                };

                this.financeDocumentsContentManager.update(updateData)
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
                this.amount = 0.00;
                this.notice = '';
                this.cost_without_tax = 0.00;
                this.cost_with_tax = 0.00;
                this.summ_without_tax = 0.00;
                this.summ_with_tax = 0.00;
                this.summ_tax = 0.00;

                this.service_id = null;
                this.product_id = null;

                this.period_type = 0;
                this.period_amount = 0;
                this.start_date = null;
                this.end_date = null;

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
            productErrors () {
                const errors = []
                if (this.product_id != null || this.service_id != null) {
                    return errors;
                }

                errors.push(this.$store.state.t('Required field'))
                return errors
            },
            serviceErrors () {
                const errors = []
                if (this.product_id != null || this.service_id != null) {
                    return errors;
                }

                errors.push(this.$store.state.t('Required field'))
                return errors
            },
            amountErrors () {
                const errors = []
                if (!this.$v.amount.$dirty) return errors
                !this.$v.amount.required && errors.push(this.$store.state.t('Required field'))
                !this.$v.amount.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
                return errors
            },
            cost_without_taxErrors () {
                const errors = []
                if (!this.$v.cost_without_tax.$dirty) return errors
                !this.$v.cost_without_tax.required && errors.push(this.$store.state.t('Required field'))
                !this.$v.cost_without_tax.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
                return errors
            },
            cost_with_taxErrors () {
                const errors = []
                if (!this.$v.cost_with_tax.$dirty) return errors
                !this.$v.cost_with_tax.required && errors.push(this.$store.state.t('Required field'))
                !this.$v.cost_with_tax.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
                return errors
            },
            summ_without_taxErrors () {
                const errors = []
                if (!this.$v.summ_without_tax.$dirty) return errors
                !this.$v.summ_without_tax.required && errors.push(this.$store.state.t('Required field'))
                !this.$v.summ_without_tax.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
                return errors
            },
            summ_taxErrors () {
                const errors = []
                if (!this.$v.summ_tax.$dirty) return errors
                !this.$v.summ_tax.required && errors.push(this.$store.state.t('Required field'))
                // !this.$v.summ_tax.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
                return errors
            },
            summ_with_taxErrors () {
                const errors = []
                if (!this.$v.summ_with_tax.$dirty) return errors
                !this.$v.summ_with_tax.required && errors.push(this.$store.state.t('Required field'))
                !this.$v.summ_with_tax.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 0.01'))
                return errors
            },
        },

        beforeDestroy () {
            this.$eventHub.$off(this.createProcessNameTrigger);
            this.$eventHub.$off(this.updateProcessNameTrigger);
        },
    }
</script>
