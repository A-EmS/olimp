<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">{{$store.state.t('Change parent...')}} {{item.name}}</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
              <v-flex xs12 sm12 md12>
                <v-autocomplete
                        v-model="newParentNodeId"
                        :error-messages="newParentNodeIdErrors"
                        :items="allNodes"
                        item-value="id"
                        item-text="name"
                        :label="$store.state.t('Parent Node')"
                ></v-autocomplete>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" flat @click="dialog = false">Close</v-btn>
          <v-btn v-on:click="confirm()" color="blue darken-1" flat>Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  import {FinanceClassesManager} from "../../../../managers/FinanceClassesManager";
  import {validationMixin} from "vuelidate";
  import {required} from "vuelidate/lib/validators";

  export default {
    mixins: [validationMixin],

    validations: {
      newParentNodeId: { required },
    },

    props: {
      handlerChangeParentProcessName: {type: String, require: true},
      handlerChangeParentOutputProcessName: {type: String, require: true},
    },
    data: () => ({
      dialog: false,
      item: {},
      newParentNodeId: null,
      allNodes: [],
    }),
    created() {
      this.financeClassesManager = new FinanceClassesManager();
      this.$eventHub.$on(this.handlerChangeParentProcessName, (item) => {
        this.dialog = true;
        this.item = item;
        this.newParentNodeId = null;
        this.financeClassesManager.getAll(item.id)
          .then( (response) => {
            // this.showCustomLoaderDialog = false;
            if (response.data !== false){
              if (!response.data.error){
                this.allNodes = response.data.items;
              } else {
                // this.openErrorDialog(response.data.error);
              }
            }
          })
          .catch(function (error) {
            console.log(error);
          });

      });
    },
    methods: {
      confirm: function () {
        this.$v.$touch();
        if (!this.$v.$invalid) {
          this.$eventHub.$emit(this.handlerChangeParentOutputProcessName, {newParentNodeId: this.newParentNodeId, movableNodeId: this.item.id});
          this.dialog = false;
        }
      },
    },
    computed: {
      newParentNodeIdErrors () {
        const errors = []
        if (!this.$v.newParentNodeId.$dirty) return errors
        !this.$v.newParentNodeId.required && errors.push(this.$store.state.t('Required field'))
        return errors
      },
    },
    beforeDestroy () {
      this.$eventHub.$off(this.handlerChangeParentProcessName);
    },
  }
</script>
