<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">{{$store.state.t('Additional Services/Products ')}}</span>
        </v-card-title>
        <v-card-text>
          <table style="font-size: 14px; font-weight: bold;">
            <tr v-for="key in keysToPrint">
              <td>{{key['name']}}</td>
            </tr>
          </table>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" flat="flat" @click="dialog = false">{{$store.state.t('Close')}}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <loadercustom :showDialog="showCustomLoaderDialog"></loadercustom>
  </v-layout>
</template>

<script>
  import loadercustom from "../../views/components/loadercustom";
  import {ContactsManager} from "../../managers/ContactsManager";
  import {FinanceDocumentsContentManager} from "../../managers/FinanceDocumentsContentManager";

  export default {
    components: {
      loadercustom,
    },
    props: {
      showDialog: Boolean,
    },

    data () {
      return {
        showCustomLoaderDialog: false,
        showProcessNameTrigger: 'showAdditionalServiceDocumentInfo',
        dialog: false,
        keysToPrint: [],
      }
    },

    created() {
      this.financeDocumentsContentManager = new FinanceDocumentsContentManager();

      if (this.$eventHub) {

        this.$eventHub.$on(this.showProcessNameTrigger, (data) => {
          this.dialog = true;
          this.showCustomLoaderDialog = true;
          this.financeDocumentsContentManager.findServicesByDocumentId(data.financeDocumentId)
                  .then( (response) => {
                    if(response.data !== false){
                      this.dialog = true;
                      this.keysToPrint = response.data.items;
                      this.showCustomLoaderDialog = false;
                    }
                  })
                  .catch(function (error) {
                    console.log(error);
                  });
        });
      }
    },

    beforeDestroy () {
      this.$eventHub.$off(this.showProcessNameTrigger);
    },
  }
</script>
