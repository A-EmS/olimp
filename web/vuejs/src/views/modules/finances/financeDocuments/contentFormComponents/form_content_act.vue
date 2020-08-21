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
                            @keyup="onChangeAmount"
                            @change="onChangeAmount"
                    ></v-text-field>
                    <br />
                    <div v-if="amountWarning !=false " class="alert alert-warning">{{$store.state.t('Available amount for choice')}} {{amountForChoice}}</div>
                    <v-btn color="success" @click="submit">{{$store.state.t('Submit')}}</v-btn>
                    <v-btn @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
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
    import { required, minValue } from 'vuelidate/lib/validators'
    import customValidationMixin from "../../../../../mixins/customValidationMixin";
    import {ServicesManager} from "../../../../../managers/ServicesManager";
    import {ProductsManager} from "../../../../../managers/ProductsManager";
    import {FinanceDocumentsContentManager} from "../../../../../managers/FinanceDocumentsContentManager";
    import constantsMixin from "../../../../../mixins/constantsMixin";
    import mathMixin from "../../../../../mixins/mathMixin";

    export default {
        components: {
            'layout-wrapper': LayoutWrapper,
            'demo-card': DemoCard,
            loadercustom
        },

        mixins: [validationMixin, customValidationMixin, constantsMixin, mathMixin],

        validations: {
            amount: { required, minValue: minValue(1) },
            // parent_content_id: { required },
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
                amountWarning: false,
                amountForChoice: null,

                parent_content_id: null,
                // parentContentItems: [],

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

            this.servicesManager = new ServicesManager();
            this.productsManager = new ProductsManager();
            this.financeDocumentsContentManager = new FinanceDocumentsContentManager();

            this.$eventHub.$on(this.createProcessNameTrigger, (data) => {
                this.header = this.$store.state.t('Creating new')+'...';
                this.initFormComponent();
                this.setDefaultData();
                this.showDialog = true;
            });

            this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
                this.initFormComponent();
                this.financeDocumentsContentManager.get(data.id)
                    .then( (response) => {
                        if(response.data !== false){
                            this.amountWarning = false;
                            this.amountForChoice = null;
                            this.rowId = response.data.id;
                            this.amount = response.data.amount;
                            this.parent_content_id = response.data.parent_content_id;
                            if(response.data.service_id > 0){
                                this.service_id = response.data.parent_content_id;
                            } else {
                                this.product_id = response.data.parent_content_id;
                            }
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
                this.getServicesProducts();
            },
            getServicesProducts: function () {
                this.financeDocumentsContentManager.getServicesProductsList(this.document_id)
                    .then( (response) => {
                        if(response.data !== false){
                            this.productItems = response.data.products;
                            this.serviceItems = response.data.services;
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            onChangeService: function(){
                this.product_id = null;
                this.parent_content_id = this.service_id;
                this.checkAmounts(this.parent_content_id);
            },
            onChangeProduct: function(){
                this.service_id = null;
                this.parent_content_id = this.product_id;
                this.checkAmounts(this.parent_content_id);
            },
            onChangeAmount: function(){
                this.checkAmounts(this.parent_content_id);
            },
            checkAmounts: function(parent_content_id) {
                this.amountWarning = false;
                this.amountForChoice = null;
                this.showCustomLoaderDialog = true;
                this.financeDocumentsContentManager.checkAmounts(this.rowId, parent_content_id)
                    .then( (response) => {
                        this.showCustomLoaderDialog = false;
                        if(response.data !== false){
                            this.amountForChoice = response.data.amount;
                            if (response.data.error !== false || parseFloat(this.amount) > parseFloat(response.data.amount)) {
                                this.amountWarning = true;
                                this.amount = response.data.amount;
                            }
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
                    parent_content_id: this.parent_content_id,
                    service_id: this.service_id,
                    product_id: this.product_id,
                    document_id: this.document_id,
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
                    parent_content_id: this.parent_content_id,
                    service_id: this.service_id,
                    product_id: this.product_id,
                    document_id: this.document_id,
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
                this.amountWarning = false;
                this.amountForChoice = null;

                this.service_id = null;
                this.product_id = null;
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
                !this.$v.amount.minValue && errors.push(this.$store.state.t('Min value has to be more or equal 1'))
                return errors
            },
            // parent_content_idErrors () {
            //     const errors = []
            //     if (!this.$v.parent_content_id.$dirty) return errors
            //     !this.$v.parent_content_id.required && errors.push(this.$store.state.t('Required field'))
            //     return errors
            // },
        },

        beforeDestroy () {
            this.$eventHub.$off(this.createProcessNameTrigger);
            this.$eventHub.$off(this.updateProcessNameTrigger);
        },
    }
</script>
