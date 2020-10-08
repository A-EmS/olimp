import axios from 'axios';
import qs from 'qs';

var PCRM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/project-completing-report/get-all', qs.stringify({}))
    },

    getByPage: function(page, perPage, filters){

        return axios.post(window.apiDomainUrl+'/project-completing-report/get-all-by-page', qs.stringify({page:page, perPage:perPage, filters:filters}))
    },

    getPayerManagers: function(){

        return axios.get(window.apiDomainUrl+'/project-completing-report/get-payer-managers', qs.stringify({}))
    },

    getProjectMangers: function(){

        return axios.get(window.apiDomainUrl+'/project-completing-report/get-project-managers', qs.stringify({}))
    },

    getPerformers: function(){

        return axios.get(window.apiDomainUrl+'/project-completing-report/get-performers', qs.stringify({}))
    },
};

export function ProjectCompletingReportManager() {
    return PCRM;
}