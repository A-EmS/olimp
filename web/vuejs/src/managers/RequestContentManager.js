import axios from 'axios';
import qs from 'qs';

var RQCM = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/request-content/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/request-content/get-by-id?id='+id, qs.stringify({}));
    },

    getAllByRequestId: function(requestId){

        return axios.get(window.apiDomainUrl+'/request-content/get-all-by-request-id?requestId='+requestId, qs.stringify({}));
    },

    getAllStagesByRequestId: function(requestId){

        return axios.get(window.apiDomainUrl+'/request-content/get-all-stages-by-request-id?requestId='+requestId, qs.stringify({}));
    },

    createLaborCosts: function(data){

        return axios.post(window.apiDomainUrl+'/request-content/create-labor-costs', qs.stringify(data));
    },

    updateLaborCosts: function(data){

        return axios.post(window.apiDomainUrl+'/request-content/update-labor-costs', qs.stringify(data));
    },

    updateStagesLaborCosts: function(data){

        return axios.post(window.apiDomainUrl+'/request-content/update-stages-labor-costs', qs.stringify(data));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/request-content/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/request-content/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/request-content/delete', qs.stringify(data));
    },
};

export function RequestContentManager() {
    return RQCM;
}