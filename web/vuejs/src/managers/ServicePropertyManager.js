import axios from 'axios';
import qs from 'qs';

var ServiceProperty = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/banks/get-all', qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/banks/get-by-id?id='+id, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/banks/create', qs.stringify(createData))
    },

    update: function(updateData){

        return axios.post(window.apiDomainUrl+'/banks/update', qs.stringify(updateData))
    },

    deleteRow: function(companyId){

        return axios.post(window.apiDomainUrl+'/banks/delete', qs.stringify({id:companyId}))
    },
};

export function ServicePropertyManager() {
    return ServiceProperty;
}