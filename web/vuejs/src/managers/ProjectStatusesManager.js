import axios from 'axios';
import qs from 'qs';

var PRSTM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/project-statuses/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/project-statuses/get-by-id?id='+id, qs.stringify({}));
    },

    getAllByCountryId: function(countryId){

        return axios.get(window.apiDomainUrl+'/project-statuses/get-all-by-country-id?countryId='+countryId, qs.stringify({}))
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/project-statuses/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/project-statuses/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/project-statuses/delete', qs.stringify(data));
    },
};

export function ProjectStatusesManager() {
    return PRSTM;
}