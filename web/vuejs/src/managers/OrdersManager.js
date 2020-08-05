import axios from 'axios';
import qs from 'qs';

var ORM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/orders/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/orders/get-by-id?id='+id, qs.stringify({}));
    },

    getAllInvoices: function(){

        return axios.get(window.apiDomainUrl+'/orders/get-all-invoices', qs.stringify({}))
    },

    getInvoiceById: function(id){

        return axios.get(window.apiDomainUrl+'/orders/get-invoice-by-id?id='+id, qs.stringify({}))
    },

    getAllTillOperations: function(tillId){

        return axios.get(window.apiDomainUrl+'/orders/get-all-till-operations?tillId='+tillId, qs.stringify({}))
    },

    getTillOperationById: function(id){

        return axios.get(window.apiDomainUrl+'/orders/get-till-operation-by-id?id='+id, qs.stringify({}))
    },


    create: function(data){

        return axios.post(window.apiDomainUrl+'/orders/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/orders/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/orders/delete', qs.stringify(data));
    },
};

export function OrdersManager() {
    return ORM;
}