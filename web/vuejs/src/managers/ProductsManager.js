import axios from 'axios';
import qs from 'qs';

var Products = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/products/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/products/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/products/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/products/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/products/delete', qs.stringify(data));
    },
};

export function ProductsManager() {
    return Products;
}