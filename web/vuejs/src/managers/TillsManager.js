import axios from 'axios';
import qs from 'qs';

var TLS = {

    loadTillForUser: function() {
        return axios.get(window.apiDomainUrl+'/tills/load-till-for-user', qs.stringify({}))
    },

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/tills/get-all', qs.stringify({}))
    },

    getAllExceptUserTill: function(){

        return axios.get(window.apiDomainUrl+'/tills/get-all-except-user-till', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/tills/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/tills/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/tills/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/tills/delete', qs.stringify(data));
    },
};

export function TillsManager() {
    return TLS;
}