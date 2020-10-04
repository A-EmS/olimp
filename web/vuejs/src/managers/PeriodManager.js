import axios from 'axios';
import qs from 'qs';

var PM = {

    getPeriodTypes: function () {
        return axios.get(window.apiDomainUrl+'/period/get-types', qs.stringify({}));
    }
};

export function PeriodManager() {
    return PM;
}