<template>
    <div>
        <v-autocomplete
                v-model="contact_id"
                :items="contactsItems"
                no-filter
                item-value="id"
                item-text="name"
                :placeholder="$store.state.t('phone / skype / email .. etc.')"
                :search-input.sync="search"
                @keyup="searchContact"
                @change="selectContact"
        >
            <template slot="item" slot-scope="row">
                <div style="font-size: 11px; color: black;">
                    {{row.item.contractor_name}} <br />
                    {{row.item.contact_type}}:
                    {{row.item.name}}
                </div>
            </template>
        </v-autocomplete>
        <search-contact-box-popup :selectSearchResultAction="selectSearchResultAction"></search-contact-box-popup>
    </div>
</template>

<style>
    .v-autocomplete__content .v-list__tile{
        height: auto !important;
        margin: 10px 0 !important;
    }
</style>

<script>
    import qs from "qs";
    import {ContactsManager} from "../../../managers/ContactsManager";
    import searchContactBoxPopup from "./SearchContactBoxPopup"

    export default {
        components: {
            searchContactBoxPopup
        },

        data() {
            return {
                selectSearchResultAction: 'select:search:contact:box',
                search: null,
                contact_id: null,
                contactsItems: [],
            }
        },

        created() {

            this.contactsManager = new ContactsManager();
        },

        methods: {
            searchContact: function () {
                if (this.search.length >= 3) {
                    this.contactsManager.findByName(this.search)
                        .then( (response) => {
                            if(response.data !== false){
                                this.contactsItems = response.data.items;
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            },
            selectContact: function () {
                let data = this.contactsItems.find(obj => obj.id === this.contact_id);
                window.dispatchEvent(new CustomEvent(this.selectSearchResultAction, { 'detail': {data} }));
                this.$nextTick(() => {
                    this.search = '';
                    this.contact_id = null;
                });
            },
        },
    }
</script>

