<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" scrollable max-width="300px">
      <v-card>
        <v-card-title><h4>Select interface Language</h4></v-card-title>
        <v-divider></v-divider>
        <v-card-text style="height: 300px;">

          <v-radio-group v-model="language" column>
            <v-radio v-for="item in items" :label="item.name" :value="item.acronim"></v-radio>
          </v-radio-group>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-btn color="blue darken-1" flat @click="selectInterfaceLanguage">Select</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <loadercustom :showDialog="showCustomLoaderDialog"></loadercustom>
  </v-layout>
</template>

<script>
  import qs from "qs";
  import axios from 'axios';
  import loadercustom from "./loadercustom";

  export default {
    components: {
      loadercustom
    },
    data () {
      return {
        language: '',
        dialog: false,
        items: [],
        showCustomLoaderDialog: false,
      }
    },
    created() {
      this.getLanguages();
    },
    methods: {
      getLanguages: function () {
        if ( typeof typeof this.$store.state.user != 'undefined' && typeof typeof this.$store.state.user.settings != 'undefined' && typeof this.$store.state.user.settings.interface_language == 'undefined'){
          axios.get(window.apiDomainUrl+'/languages/get-all-languages', qs.stringify({}))
                  .then((response) => {
                    this.items = response.data.items;
                    this.dialog = true;
          });
        } else if ( typeof this.$store.state.currentInterfaceVocabulary == 'undefined') {
          this.setInterfaceVocabulary(this.$store.state.user.settings.interface_language);
        }
      },
      selectInterfaceLanguage: function(){
        axios.post(window.apiDomainUrl + '/user/set-user-setting', qs.stringify({
            user_id: this.$store.state.user.id,
            key: 'interface_language',
            value: this.language
          }))
            .then((response) => {
              if (response.data !== false) {

                this.$store.state.user.settings.interface_language = this.language;
                this.setInterfaceVocabulary(this.language);
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
      setInterfaceVocabulary: function (language) {
        this.showCustomLoaderDialog = true;
        axios.get(window.apiDomainUrl+'/interface-vocabularies/get-interface-vocabulary?language='+language, qs.stringify({}))
                .then( (response) => {
                  if(response.data !== false){
                    this.$store.state.currentInterfaceVocabulary = response.data;

                    this.dialog = false;
                    this.showCustomLoaderDialog = false;

                    vue.$forceUpdate();
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      }
    },
  }
</script>
