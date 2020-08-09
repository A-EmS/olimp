import axios from 'axios';
import qs from 'qs';

var TXS = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/taxes/get-all', qs.stringify({}))
    },
};

export function TaxesManager() {
    return TXS;
}