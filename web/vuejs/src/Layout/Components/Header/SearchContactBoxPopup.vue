<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">{{$store.state.t('Expanded Info')}}</span>
        </v-card-title>
        <v-card-text>
          <table style="font-size: 18px; font-weight: bold;">
            <tr v-for="key in keysToPrint">
              <td>{{$store.state.t(key)}}:</td>
              <td v-if="key != 'Entities For Individuals'"><u style="margin-left: 20px">{{dataToPrint[key]}}</u></td>
              <td style="padding-top: 23px" v-else>
                <table>
                  <tr v-for="ent in dataToPrint['Entities For Individuals']">
                    <td>
                      <u style="margin-left: 20px">
                        {{ent.entity_type_name}} {{ent.entity_short_name}} <span style="color: darkslategrey">({{ent.entity_type_name}} {{ent.entity_name}})</span>
                      </u>
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
  </v-layout>
</template>

<script>
  import {ContactsManager} from "../../../managers/ContactsManager";

  export default {
    props: {
      selectSearchResultAction: {type: String, require: false},
    },

    data () {
      return {
        dialog: false,
        keysToPrint: [],
        dataToPrint: {},
      }
    },

    created() {

      this.contactsManager = new ContactsManager();

      window.addEventListener(this.selectSearchResultAction,  (e) => {
        this.contactsManager.findForHeaderSearch(e.detail.data.id)
                .then( (response) => {
                  if(response.data !== false){
                    this.dialog = true;
                    this.keysToPrint = Object.keys(response.data.items);
                    this.dataToPrint = response.data.items;
                  }
                })
                .catch(function (error) {
                  console.log(error);
                });
      });
    },

    beforeDestroy () {
      window.removeEventListener(this.selectSearchResultAction, (e) => {this.dialog = true;});
    },
  }
</script>
