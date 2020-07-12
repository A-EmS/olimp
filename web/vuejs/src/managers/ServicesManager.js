import axios from 'axios';
import qs from 'qs';

var Services = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/services/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/services/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/services/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/services/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/services/delete', qs.stringify(data));
    },
};

export function ServicesManager() {
    return Services;
}