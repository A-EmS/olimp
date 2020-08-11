import axios from 'axios';
import qs from 'qs';

var FinanceDocuments = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/finance-documents/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/finance-documents/get-by-id?id='+id, qs.stringify({}))
    },

    getByPage: function(page, perPage, filters){

        return axios.post(window.apiDomainUrl+'/finance-documents/get-all-by-page', qs.stringify({page:page, perPage:perPage, filters:filters}))
    },

    getAllByTerm: function(term, rowId){

        return axios.get(window.apiDomainUrl+'/finance-documents/get-all-by-term?term='+term+'&id='+rowId, qs.stringify({}))
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