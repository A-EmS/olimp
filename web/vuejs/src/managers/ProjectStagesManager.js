import axios from 'axios';
import qs from 'qs';

var PRSM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/project-stages/get-all', qs.stringify({}))
    },

    getAllByCountryId: function(countryId){

        return axios.get(window.apiDomainUrl+'/project-stages/get-all-by-country-id?countryId='+countryId, qs.stringify({}))
    },

    getAllCodesForSelect: function(){

        return axios.get(window.apiDomainUrl+'/project-stages/get-all-codes-for-select', qs.stringify({}))
    },

    getAllCodesForSelectAccordingRequest: function(requestId){

        return axios.get(window.apiDomainUrl+'/project-stages/get-all-codes-for-select-according-request?requestId='+requestId, qs.stringify({}))
    },

    getAllCodesForSelectForProjectParts: function(){

        return axios.get(window.apiDomainUrl+'/project-stages/get-all-codes-for-select-for-project-parts', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/project-stages/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/project-stages/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/project-stages/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/project-stages/delete', qs.stringify(data));
    },
};

export function ProjectStagesManager() {
    return PRSM;
}