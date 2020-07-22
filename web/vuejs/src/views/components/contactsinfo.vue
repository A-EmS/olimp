<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">{{$store.state.t('Expanded Info')}}</span>
        </v-card-title>
        <v-card-text>
          <table style="font-size: 14px; font-weight: bold;">
            <tr v-for="key in keysToPrint">
              <td>{{$store.state.t(key)}}</td>
              <td v-if="key != 'Entities For Individuals'"><u style="margin-left: 20px">{{dataToPrint[key]}}</u></td>
              <td style="" v-else>
                <table>
                  <tr v-for="ent in dataToPrint['Entities For Individuals']">
                    <td>
                      <u style="margin-left: 20px">
                        {{ent.entity_type_name}} {{ent.entity_short_name}} <span style="color: darkslategrey">({{ent.entity_type_name}} {{ent.entity_name}})</span>
                      </u>;
                    </td>
                  </tr>
                </table>
              </td>
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
        showProcessNameTrigger: 'showContactsInfo',
        dialog: false,
        keysToPrint: [],
        dataToPrint: {},
      }
    },

    created() {
      this.contactsManager = new ContactsManager();

      if (this.$eventHub) {
        this.$eventHub.$on(this.showProcessNameTrigger, (data) => {
          this.showCustomLoaderDialog = true;
          this.contactsManager.findByContractorId(data.id)
                  .then( (response) => {
                    if(response.data !== false){
                      this.dialog = true;
                      this.keysToPrint = Object.keys(response.data.items);
                      this.dataToPrint = response.data.items;
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
