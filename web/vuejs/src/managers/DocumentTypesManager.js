import axios from 'axios';
import qs from 'qs';

var DocumentTypes = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/document-types/get-all', qs.stringify({}))
    },

    getScenarioTypes: function(){

        return axios.get(window.apiDomainUrl+'/document-types/get-scenario-types', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/document-types/get-by-id?id='+id, qs.stringify({}));
    },

    getByCountryId: function(countryId){

        return axios.get(window.apiDomainUrl+'/document-types/get-by-country-id?country_id='+countryId, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/document-types/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/document-types/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/document-types/delete', qs.stringify(data));
    },
};

export function DocumentTypesManager() {
    return DocumentTypes;
}