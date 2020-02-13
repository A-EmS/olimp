import axios from 'axios';
import qs from 'qs';

var ATM = {
    getForSelect: function(string){

        var createData = {

        };
        return axios.get(window.apiDomainUrl+'/address-types/get-all-for-select', qs.stringify({}))
    },
};

export function AddressTypesManager() {
    return ATM;
}