import axios from 'axios';
import qs from 'qs';

var CRM = {

    getByFilter: function(filters){

        return axios.post(window.apiDomainUrl+'/consolidated-report/get-all-by-filter', qs.stringify({filters:filters}))
    },

};

export function ConsolidatedReportManager() {
    return CRM;
}