import axios from 'axios';
import qs from 'qs';

var CNTP = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/construction-types/get-all', qs.stringify({}))
    },

    getAllByCountry: function(countryId){

        return axios.get(window.apiDomainUrl+'/construction-types/get-all-by-country?countryId='+countryId, qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/construction-types/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/construction-types/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/construction-types/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/construction-types/delete', qs.stringify(data));
    },
};

export function ConstructionTypesManager() {
    return CNTP;
}