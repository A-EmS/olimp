import axios from 'axios';
import qs from 'qs';

var PRPM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/project-parts/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/project-parts/get-by-id?id='+id, qs.stringify({}));
    },

    getAllByStageId: function(stageId){

        return axios.get(window.apiDomainUrl+'/project-parts/get-all-by-stage-id?stageId='+stageId, qs.stringify({}));
    },

    getAllCodesForSelectAccordingCountry: function(countryId){

        return axios.get(window.apiDomainUrl+'/project-parts/get-all-codes-for-select-according-country?countryId='+countryId, qs.stringify({}))
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/project-parts/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/project-parts/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/project-parts/delete', qs.stringify(data));
    },
};

export function ProjectPartsManager() {
    return PRPM;
}