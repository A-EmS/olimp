<template>
  <v-layout row justify-center>
    <v-dialog v-model="showCalendarDialog" persistent max-width="600px">
      <template v-slot:activator="{ on }">
<!--        <v-btn color="primary" dark v-on="on">Open Dialog</v-btn>-->
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">
            {{$store.state.t('Date: ')}} <u>{{formDataItem.date}}</u>
            <span v-if="formDataItem.selected"> {{$store.state.t('Change To Day Off')}}</span>
            <span v-else> {{$store.state.t('Change To Working Day')}}</span>
          </span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
              <v-flex xs12>
                <v-textarea
                  v-model="formDataItem.notice"
                  box
                  :label="$store.state.t('Notice')"
                  value="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse"
                ></v-textarea>
              </v-flex>
            </v-layout>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" flat @click="cancel">{{$store.state.t('Cancel')}}</v-btn>
          <v-btn color="blue darken-1" flat @click="save">{{$store.state.t('Save')}}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  export default {
    props: {
      formDataItem: {type: Object, require: false, default: {}},
    },
    data: () => ({
      showCalendarDialog: false,
      updateProcessNameTrigger: 'updateCalendarItemEvent'
    }),
    created() {
      if (this.$eventHub) {
        this.$eventHub.$on(this.updateProcessNameTrigger, (data) => {
          this.showCalendarDialog = true;
        });
      }
    },
    methods: {
      cancel () {
        this.formDataItem.action = 'cancel';
        this.$eventHub.$emit('resultCalendarFormEvent', {});
        this.showCalendarDialog = false;
      },
      save () {
        this.formDataItem.action = 'save';
        this.$eventHub.$emit('resultCalendarFormEvent', {});
        this.showCalendarDialog = false;
      },
    },
    beforeDestroy () {
      this.$eventHub.$off(this.updateProcessNameTrigger);
    },
  }
</script>
