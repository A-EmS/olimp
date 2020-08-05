import axios from 'axios';
import qs from 'qs';

var FinanceDocumentsContent = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/finance-documents-content/get-all', qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/finance-documents-content/get-by-id?id='+id, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/finance-documents-content/create', qs.stringify(createData))
    },

    update: function(updateData){

        return axios.post(window.apiDomainUrl+'/finance-documents-content/update', qs.stringify(updateData))
    },

    delete: function(companyId){

        return axios.post(window.apiDomainUrl+'/finance-documents-content/delete', qs.stringify({id:companyId}))
    },
};

export function FinanceDocumentsContentManager() {
    return FinanceDocumentsContent;
}