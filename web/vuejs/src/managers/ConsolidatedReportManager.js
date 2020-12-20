import axios from 'axios';
import qs from 'qs';

var CRM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/consolidated-report/get-all', qs.stringify({}))
    },

    getByPage: function(page, perPage, filters){

        return axios.post(window.apiDomainUrl+'/consolidated-report/get-all-by-page', qs.stringify({page:page, perPage:perPage, filters:filters}))
    },

    getPayerManagers: function(){

        return axios.get(window.apiDomainUrl+'/consolidated-report/get-payer-managers', qs.stringify({}))
    },

    getProjectMangers: function(){

        return axios.get(window.apiDomainUrl+'/consolidated-report/get-project-managers', qs.stringify({}))
    },

    getPerformers: function(){

        return axios.get(window.apiDomainUrl+'/consolidated-report/get-performers', qs.stringify({}))
    },
};

export function ConsolidatedReportManager() {
    return CRM;
}