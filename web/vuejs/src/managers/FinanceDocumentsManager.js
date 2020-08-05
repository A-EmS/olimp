import axios from 'axios';
import qs from 'qs';

var FinanceDocuments = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/finance-documents/get-all', qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/finance-documents/get-by-id?id='+id, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/finance-documents/create', qs.stringify(createData))
    },

    update: function(updateData){

        return axios.post(window.apiDomainUrl+'/finance-documents/update', qs.stringify(updateData))
    },

    delete: function(companyId){

        return axios.post(window.apiDomainUrl+'/finance-documents/delete', qs.stringify({id:companyId}))
    },
};

export function FinanceDocumentsManager() {
    return FinanceDocuments;
}