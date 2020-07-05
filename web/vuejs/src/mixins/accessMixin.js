import {RolesManager} from "../managers/RolesManager";

export default {
    data: () => ({
        ACL: {},
    }),
    created: function() {
        this.rolesManager = new RolesManager();
        this.loadACL();
    },
    methods: {
        loadACL: function (accessLabelId) {
            if (typeof accessLabelId === 'undefined') {
                this.ACL = {};
                return ;
            }
            this.rolesManager.getAccessList(accessLabelId)
                .then( (response) => {
                    if(response.data !== false){
                        this.ACL = response.data.items;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        getACL: function () {
            return this.ACL;
        }
    }
}

