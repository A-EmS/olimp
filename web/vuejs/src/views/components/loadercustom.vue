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
        showProcessNameTrigger: 'showMessage',
      }
    },

    props: {
      showDialog: Boolean,
      frontString: {type: String, require: false, default: 'Please stand by'},
    },
    created() {
      if (this.$eventHub) {
        this.$eventHub.$on(this.showProcessNameTrigger, (data) => {
          var time = data.time;
          this.openErrorDialog(data.message, time)
        });
      }
    },
    methods: {
      openErrorDialog(message, time){
        var dialogTime = time || 5000;
        this.frontString = this.$store.state.t(message);
        this.showDialog = true;
        setTimeout(() => {
          this.showDialog = false;
        }, dialogTime);
      },
    },
    beforeDestroy () {
      this.$eventHub.$off(this.showProcessNameTrigger);
    },
  }
</script>
