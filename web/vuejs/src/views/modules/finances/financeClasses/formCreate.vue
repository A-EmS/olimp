<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">{{$store.state.t('Create...')}}{{item.name}}</span>
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
                  v-model="item.payment_operation_type_id"
                  :readonly="item.depth !== 0"
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
          <v-btn color="blue darken-1" flat @click="dialog = false">Close</v-btn>
          <v-btn v-on:click="confirm()" color="blue darken-1" flat @click="dialog = false">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  export default {
    props: {
      handlerCreateProcessName: {type: String, require: true},
      handlerCreateOutputProcessName: {type: String, require: true},
    },
    data: () => ({
      dialog: false,
      item: {name:'', priority:0, payment_operation_type_id: '1'},
      parentNodeId: 0
    }),
    methods: {
      confirm: function () {
        this.$eventHub.$emit(this.handlerCreateOutputProcessName, {parentNodeId: this.parentNodeId, createdItemName: this.item.name, priority: this.item.priority, payment_operation_type_id: this.item.payment_operation_type_id});
        this.item.name = '';
        this.item.priority = 0;
        this.item.payment_operation_type_id = '1';
        this.dialog = false;
      },
    },
    created() {
      this.$eventHub.$on(this.handlerCreateProcessName, (data) => {
        this.parentNodeId = data.id;
        if (data.depth !== 0) {
          this.item.payment_operation_type_id = data.payment_operation_type_id.toString();
        }
        this.item.depth = data.depth;
        this.dialog = true;
      });
    },
    beforeDestroy () {
      this.$eventHub.$off(this.handlerUpdateProcessName);
    },
  }
</script>
