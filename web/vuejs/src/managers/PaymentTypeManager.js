import axios from 'axios';
import qs from 'qs';

var PMT = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/payment-type/get-all', qs.stringify({}))
    },
};

export function PaymentTypeManager() {
    return PMT;
}