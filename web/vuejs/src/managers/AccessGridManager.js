import axios from 'axios';
import qs from 'qs';

var AGM = {

    getTable: function(){

        return axios.get(window.apiDomainUrl+'/access-grid/get-table', qs.stringify({}))
    },

    getByRoleId: function(id){

        return axios.get(window.apiDomainUrl+'/access-grid/get-by-role-id?id='+id, qs.stringify({}));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/access-grid/update', qs.stringify(data));
    },

};

export function AccessGridManager() {
    return AGM;
}