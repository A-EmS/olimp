import axios from 'axios';
import qs from 'qs';

var PCRM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/project-completing-report/get-all', qs.stringify({}))
    },
};

export function ProjectCompletingReportManager() {
    return PCRM;
}