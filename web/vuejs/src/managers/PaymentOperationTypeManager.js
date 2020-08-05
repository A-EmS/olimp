import axios from 'axios';
import qs from 'qs';

var PaymentOperationType = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/payment-operation-type/get-all', qs.stringify({}))
    },
};

export function PaymentOperationTypeManager() {
    return PaymentOperationType;
}