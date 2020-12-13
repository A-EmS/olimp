import axios from 'axios';
import qs from 'qs';

var OwnCompanies = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/own-companies/get-all', qs.stringify({}))
    },

    getAllWithUsersCompanies: function(){

        return axios.get(window.apiDomainUrl+'/own-companies/get-all-with-user-companies', qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/own-companies/get-by-id?id='+id, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/own-companies/create', qs.stringify(createData))
    },

    update: function(updateData){

        return axios.post(window.apiDomainUrl+'/own-companies/update', qs.stringify(updateData))
    },

    deleteRow: function(companyId){

        return axios.post(window.apiDomainUrl+'/own-companies/delete', qs.stringify({id:companyId}))
    },
};

export function OwnCompaniesManager() {
    return OwnCompanies;
}