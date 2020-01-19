<template>
  <v-layout row justify-center>
    <v-dialog
      v-model="showDialog"
      max-width="290"
    >
      <v-card>
        <v-card-title class="headline">{{confirmatorData.titleString}}</v-card-title>

        <v-card-text>
          {{confirmatorData.confirmString}}
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
            color="green darken-1"
            flat="flat"
            @click="unconfirm()"
          >
            Disagree
          </v-btn>

          <v-btn
            color="green darken-1"
            flat="flat"
            @click="confirm()"
          >
            Agree
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>

  export default {
    props: {
      handlerInputProcessName: {type: String, require: true},
      handlerOutputProcessName: {type: String, require: true},
    },

    data () {
      return {
        showDialog: false,
        confirmatorData: {titleString: '', confirmString: '', idToConfirm: 0},
      }
    },

    created() {
      this.$eventHub.$on(this.handlerInputProcessName, (data) => {
        this.confirmatorData.confirmString = (data && data.confirmString) ? data.confirmString : 'Confirm ?';
        this.confirmatorData.titleString = (data && data.titleString) ? data.titleString : 'Confirm process... ?';
        this.confirmatorData.idToConfirm = (data && data.idToConfirm) ? data.idToConfirm : 0;
        this.showDialog = true;
      });
    },

    methods: {
      unconfirm: function () {
        this.showDialog = false;
      },
      confirm: function () {
        this.$eventHub.$emit(this.handlerOutputProcessName, {id: this.confirmatorData.idToConfirm});
        this.showDialog = false;
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.handlerInputProcessName);
    },
  }
</script>
