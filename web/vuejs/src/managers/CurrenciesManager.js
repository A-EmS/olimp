import axios from 'axios';
import qs from 'qs';

var Currencies = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/currencies/get-all', qs.stringify({}))
    },

    get: function(id){

        return axios.get(window.apiDomainUrl+'/currencies/get-by-id?id='+id, qs.stringify({}))
    },
};

export function CurrenciesManager() {
    return Currencies;
}