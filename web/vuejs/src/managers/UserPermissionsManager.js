import axios from 'axios';
import qs from 'qs';

var UserPermissions = {

    getAllByUserId: function(userId){

        return axios.get(window.apiDomainUrl+'/user-permissions/get-all-by-user-id?userId='+userId, qs.stringify({}))
    },

    change: function(objectData){

        return axios.post(window.apiDomainUrl+'/user-permissions/change', qs.stringify({data:objectData}))
    },
};

export function UserPermissionsManager() {
    return UserPermissions;
}