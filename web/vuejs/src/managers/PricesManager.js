import axios from 'axios';
import qs from 'qs';

var PM = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/prices/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/prices/get-by-id?id='+id, qs.stringify({}));
    },

    getDataByCountry: function(countryId){

        return axios.get(window.apiDomainUrl+'/prices/get-data-by-country?countryId='+countryId, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/prices/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/prices/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/prices/delete', qs.stringify(data));
    },
};

export function PricesManager() {
    return PM;
}