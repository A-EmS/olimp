import axios from 'axios';
import qs from 'qs';

var UserOwnCompanies = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/user-own-companies/get-all', qs.stringify({}))
    },

    getAllByUserId: function(userId){

        return axios.get(window.apiDomainUrl+'/user-own-companies/get-all-by-user-id?userId='+userId, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/user-own-companies/create', qs.stringify(createData))
    },

    deleteRow: function(userOwnCompanyId){

        return axios.post(window.apiDomainUrl+'/user-own-companies/delete', qs.stringify({id:userOwnCompanyId}))
    },
};

export function UserOwnCompaniesManager() {
    return UserOwnCompanies;
}