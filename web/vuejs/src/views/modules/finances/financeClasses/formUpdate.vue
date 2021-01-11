<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">{{$store.state.t('Update...')}}{{item.name}}</span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
            <v-layout wrap>
              <v-flex xs12 sm12 md12>
                <v-text-field v-model="item.name" required></v-text-field>
              </v-flex>
              <v-flex xs12 sm12 md12>
                <v-text-field type="number" class="inputPrice" v-model="item.priority" required></v-text-field>
              </v-flex>
              <v-select
                  v-if="item.depth !== 0"
                  v-model="item.payment_operation_type_id"
                  :readonly="item.depth !== 1"
                  :items="$store.state.paymentOperationTypeItems"
                  item-value="id"
                  item-text="name"
                  :label="$store.state.t('Payment Operation Type')"
              ></v-select>
            </v-layout>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn v-on:click="cancel()" color="blue darken-1" flat @click="dialog = false">Close</v-btn>
          <v-btn v-on:click="confirm()" color="blue darken-1" flat @click="dialog = false">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  export default {
    props: {
      handlerUpdateProcessName: {type: String, require: true},
      handlerUpdateOutputProcessName: {type: String, require: true},
    },
    data: () => ({
      dialog: false,
      itemInitName: '',
      initPriority: '',
      initPaymentOperationTypeId: null,
      item: {}
    }),
    methods: {
      confirm: function () {
        this.$eventHub.$emit(this.handlerUpdateOutputProcessName, {item: this.item});
        this.dialog = false;
      },
      cancel: function () {
        this.item.name = this.itemInitName;
        this.item.priority = this.initPriority;
        this.item.payment_operation_type_id = this.initPaymentOperationTypeId;
        this.dialog = false;
      },
    },
    created() {
      this.$eventHub.$on(this.handlerUpdateProcessName, (data) => {
        this.dialog = true;
        data.payment_operation_type_id = data.payment_operation_type_id.toString();

        this.item = data;
        this.itemInitName = data.name;
        this.initPriority = data.priority;
        this.initPaymentOperationTypeId = data.payment_operation_type_id;
      });
    },
    beforeDestroy () {
      this.$eventHub.$off(this.handlerUpdateProcessName);
    },
  }
</script>
