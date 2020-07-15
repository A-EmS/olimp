import {RolesManager} from "../managers/RolesManager";

export default {
    data: () => ({
        ACL: {},
        loadingProcess: false
    }),
    created: function() {
        this.rolesManager = new RolesManager();
        this.loadACL();
    },
    methods: {
        loadACL: function (accessLabelId) {
            this.loadingProcess = true;
            if (typeof accessLabelId === 'undefined') {
                this.ACL = {};
                return ;
            }
            this.rolesManager.getAccessList(accessLabelId)
                .then( (response) => {
                    if(response.data !== false){
                        this.ACL = response.data.items;
                    }
                    this.loadingProcess = false;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        getACL: function () {
            return this.ACL;
        },
        updateOrShowInfoAccess: function () {
            return (this.ACL.update === true || this.ACL.showInfo === true);
        },
        showInfoAndNotUpdateAccess: function () {
            return (!this.ACL.update && this.ACL.showInfo);
        },
        listOnlyAccess: function () {
            return this.ACL.list && !this.ACL.update && !this.ACL.create && !this.ACL.delete && !this.ACL.dataExport;
        },
        updateAccess: function () {
            return this.ACL.update === true;
        }
    }
}

