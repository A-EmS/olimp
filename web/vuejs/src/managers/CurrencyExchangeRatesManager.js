import axios from 'axios';
import qs from 'qs';

var CurrencyExchangeRates = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/currency-exchange-rates/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/currency-exchange-rates/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/currency-exchange-rates/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/currency-exchange-rates/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/currency-exchange-rates/delete', qs.stringify(data));
    },
};

export function CurrencyExchangeRatesManager() {
    return CurrencyExchangeRates;
}