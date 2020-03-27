import axios from 'axios';
import qs from 'qs';

var Banks = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/banks/get-all', qs.stringify({}))
    },

    getAllByCountryId: function(countryId){

        return axios.get(window.apiDomainUrl+'/banks/get-all-by-country-id?countryId='+countryId, qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/banks/get-by-id?id='+id, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/banks/create', qs.stringify(createData))
    },

    update: function(updateData){

        return axios.post(window.apiDomainUrl+'/banks/update', qs.stringify(updateData))
    },

    deleteRow: function(companyId){

        return axios.post(window.apiDomainUrl+'/banks/delete', qs.stringify({id:companyId}))
    },
};

export function BanksManager() {
    return Banks;
}