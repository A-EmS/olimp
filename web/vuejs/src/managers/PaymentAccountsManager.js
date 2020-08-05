import axios from 'axios';
import qs from 'qs';

var PaymentAccounts = {

    getAll: function(conditionalString){

        return axios.get(window.apiDomainUrl+'/payment-accounts/get-all'+conditionalString, qs.stringify({}))
    },

    getAllByOwnCompanyId: function(ownCompanyId){

        return axios.get(window.apiDomainUrl+'/payment-accounts/get-all-by-own-company-id?ownCompanyId='+ownCompanyId, qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/payment-accounts/get-by-id?id='+id, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/payment-accounts/create', qs.stringify(createData))
    },

    update: function(updateData){

        return axios.post(window.apiDomainUrl+'/payment-accounts/update', qs.stringify(updateData))
    },

    deleteRow: function(id){

        return axios.post(window.apiDomainUrl+'/payment-accounts/delete', qs.stringify({id:id}))
    },
};

export function PaymentAccountsManager() {
    return PaymentAccounts;
}