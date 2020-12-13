<template>
  <div>
    <v-radio-group row>
      <span v-for="item in userPermissionsItems" style="margin-right: 30px; cursor: pointer">
        <input
            type="checkbox"
            value=""
            :id=getCheckBoxId(item)
            :name=item.key
            :checked=getChecked(item)
            @change="onChecBoxChange(item, $event)"
            style="margin-right: 5px"
        >
        <label style="cursor: pointer" :for=getCheckBoxId(item)>{{$store.state.t(item.label)}}</label>
      </span>
    </v-radio-group>
    <loadercustom :showDialog="showCustomLoaderDialog" :frontString="customDialogfrontString"></loadercustom>
  </div>
</template>

<script>

  import loadercustom from "../../components/loadercustom";
  import {UserPermissionsManager} from "@/managers/UserPermissionsManager";

  export default {
    components: {
      loadercustom,
    },
    data () {
      return {
        showCustomLoaderDialog: false,
        customDialogfrontString: 'Please stand by',
        userPermissionsItems: [],

      }
    },
    props: {
      userId: {type: Number, require: true},
    },
    mounted() {
      this.userPermissionsManager = new UserPermissionsManager();
      this.getUserPermissions();
    },
    methods: {
      getCheckBoxId: function (item) {
        return 'perm_'+item.key;
      },
      getChecked: function (item) {
        return parseInt(item.status) === 1;
      },
      onChecBoxChange: function (item, event) {
        this.showCustomLoaderDialog = true;
        item.status = 0;
        if (event.target.checked === true) {
          item.status = 1;
        }

        this.showCustomLoaderDialog = true;
        this.userPermissionsManager.change(item)
        .then(() => {
          this.showCustomLoaderDialog = false;
        })
      },
      getUserPermissions: function () {
        var tempArray = [];
        this.userPermissionsManager.getAllByUserId(this.userId)
            .then( (response) => {
              if(response.data !== false){
                let userPermissionsItems = response.data.items;
                let allPermissionsItems = response.data.permissions;

                Object.keys(allPermissionsItems).forEach((itemKey, index) => {

                  let permissionId = 0
                  let permissionStatus = 0
                  if (typeof userPermissionsItems[itemKey] !== 'undefined'){
                    permissionId = parseInt(userPermissionsItems[itemKey]['id']);
                  }
                  if (typeof userPermissionsItems[itemKey] !== 'undefined'){
                    permissionStatus = parseInt(userPermissionsItems[itemKey]['status']);
                  }

                  tempArray.push({
                    key: itemKey,
                    label: allPermissionsItems[itemKey],
                    id: permissionId || 0,
                    status: permissionStatus || 0,
                    userId: this.userId,
                  });
                });

                this.userPermissionsItems = tempArray;
              }
            })
            .catch(function (error) {
              console.log(error);
            });
      },
    },

    beforeDestroy () {

    },
  }
</script>
