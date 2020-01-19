<template>
  <div class="text-xs-center">
    <v-dialog
      v-model="showDialog"
      hide-overlay
      persistent
      width="300"
    >
      <v-card
        color="primary"
        dark
      >
        <v-card-text>
          {{frontString}}
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
  export default {
    data () {
      return {

      }
    },

    props: {
      showDialog: Boolean,
      frontString: {type: String, require: false, default: 'Please stand by'},
    },
  }
</script>
