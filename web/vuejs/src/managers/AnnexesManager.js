import axios from 'axios';
import qs from 'qs';

var Annexes = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/acts/get-all', qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/acts/get-by-id?id='+id, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/acts/create', qs.stringify(createData))
    },

    update: function(updateData){

        return axios.post(window.apiDomainUrl+'/acts/update', qs.stringify(updateData))
    },

    deleteRow: function(companyId){

        return axios.post(window.apiDomainUrl+'/acts/delete', qs.stringify({id:companyId}))
    },
};

export function AnnexesManager() {
    return Annexes;
}