import axios from 'axios';
import qs from 'qs';

var CTM = {
    getAll: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all', qs.stringify({}))

    },

    getAllForSelect: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select', qs.stringify({}))

    },

    getForSelectAccordingEntityTypes: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select-according-entity-types', qs.stringify({}))

    },
    getForSelectAccordingRegions: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select-according-region', qs.stringify({}))

    },

    getForSelectAccordingEntities: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select-according-entities', qs.stringify({}))

    },

    getForSelectAccordingBanks: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select-according-banks', qs.stringify({}))

    },

    getAllPhoneCodeList: function(){

        return axios.get(window.apiDomainUrl+'/countries/get-all-phone-code-list', qs.stringify({}))

    },
};

export function CountriesManager() {
    return CTM;
}