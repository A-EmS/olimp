<template>
  <v-layout row justify-center>
    <v-dialog
      v-model="showDialog"
      max-width="290"
    >
      <v-card>
        <v-card-title class="headline">{{$store.state.t('Patterns')}}</v-card-title>

        <v-card-text>
          {{$store.state.t('Choose your pattern to generate commercial offering')}}
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

  export default {
    props: {
      handlerInputProcessName: {type: String, require: true},
      handlerOutputProcessName: {type: String, require: true},
    },

    data () {
      return {
        showDialog: false,
      }
    },

    created() {
      this.$eventHub.$on(this.handlerInputProcessName, (data) => {

        this.showDialog = true;
      });
    },

    methods: {
      unconfirm: function () {
        this.showDialog = false;
      },
      confirm: function () {
        // this.$eventHub.$emit(this.handlerOutputProcessName, {});
        this.showDialog = false;
      },
    },

    beforeDestroy () {
      this.$eventHub.$off(this.handlerInputProcessName);
    },
  }
</script>
