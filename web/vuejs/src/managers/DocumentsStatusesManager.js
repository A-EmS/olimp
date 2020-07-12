import axios from 'axios';
import qs from 'qs';

var DocumentStatuses = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/documents-statuses/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/documents-statuses/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/documents-statuses/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/documents-statuses/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/documents-statuses/delete', qs.stringify(data));
    },
};

export function DocumentStatusesManager() {
    return DocumentStatuses;
}