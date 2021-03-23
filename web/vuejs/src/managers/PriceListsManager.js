import axios from 'axios';
import qs from 'qs';

var PL = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/price-lists/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/price-lists/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/price-lists/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/price-lists/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/price-lists/delete', qs.stringify(data));
    },
};

export function PriceListsManager() {
    return PL;
}