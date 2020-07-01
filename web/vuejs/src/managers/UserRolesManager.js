import axios from 'axios';
import qs from 'qs';

var URM = {

    getByUserId: function(id){

        return axios.get(window.apiDomainUrl+'/user-role/get-by-user-id?id='+id, qs.stringify({}));
    },

};

export function UserRolesManager() {
    return URM;
}