import axios from 'axios';
import qs from 'qs';

var FNA = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/finance-actions/get-all', qs.stringify({}))
    },
};

export function FinanceActionsManager() {
    return FNA;
}