import axios from 'axios';
import qs from 'qs';

var FinanceBook = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/finance-book/get-all', qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/finance-book/get-by-id?id='+id, qs.stringify({}))
    },

    getInfoByPageAndFilters: function(page, perPage, filters){

        return axios.post(window.apiDomainUrl+'/finance-book/get-info-by-page-and-filters', qs.stringify({page:page, perPage:perPage, filters:filters}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/finance-book/create', qs.stringify(createData))
    },
};

export function FinanceBookManager() {
    return FinanceBook;
}