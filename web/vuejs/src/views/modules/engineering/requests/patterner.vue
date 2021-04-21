<template>
  <v-layout row justify-center>
    <v-dialog
      v-model="showDialog"
      max-width="290"
    >
      <v-card>
        <v-card-title class="headline">{{$store.state.t('Patterns')}}</v-card-title>

        <v-card-text>
          <v-autocomplete
              v-model="pattern_id"
              :items="patterns_Items"
              item-value="id"
              item-text="name"
              :label="$store.state.t('Pattern')"
          ></v-autocomplete>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
            color="green darken-1"
            flat="flat"
            @click="confirm()"
          >
            {{$store.state.t('Generate')}}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>

  import {PatternsManager} from "@/managers/PatternsManager";
  import {DocumentGeneratorManager} from "@/managers/DocumentGeneratorManager";

  export default {
    props: {
      handlerInputProcessName: {type: String, require: true},
      handlerOutputProcessName: {type: String, require: true},
      updateCustomEventName: {type: String, require: false, default: ''},
    },

    data () {
      return {
        showDialog: false,

        request_id: null,
        country_id: null,
        own_company_id: null,
        document_type_id: null,
        price_list_id: null,

        patterns_Items: [],
        pattern_id: null,
      }
    },

    created() {
      this.patternsManager = new PatternsManager();
      this.documentGeneratorManager = new DocumentGeneratorManager();

      this.$eventHub.$on(this.handlerInputProcessName, (data) => {
        this.request_id = data.request_id;
        this.country_id = data.country_id;
        this.own_company_id = data.own_company_id;
        this.document_type_id = data.document_type_id;
        this.price_list_id = data.price_list_id;

        this.patternsManager.getForSelect(data)
            .then( (response) => {
              if(response.data !== false){
                this.patterns_Items = response.data.items;
              }
            })
            .catch(function (error) {
              console.log(error);
            });

        this.showDialog = true;
      });
    },

    methods: {
      confirm: function () {

        if (this.pattern_id == null) {
          return;
        }

        var dataForDocument = {
          request_id: this.request_id,
          price_list_id: this.price_list_id,
          document_type_id: this.document_type_id,
          pattern_id: this.pattern_id,
        }

        this.documentGeneratorManager.generate(dataForDocument)
            .then( (response) => {
              if(response.data !== false){
                this.$eventHub.$emit(this.updateCustomEventName);
                this.documentGeneratorManager.download(this.request_id, this.document_type_id);
              }
            })
            .catch(function (error) {
              console.log(error);
            });
        this.showDialog = false;
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.handlerInputProcessName);
    },
  }
</script>
