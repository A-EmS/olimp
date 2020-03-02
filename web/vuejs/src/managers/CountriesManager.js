import axios from 'axios';
import qs from 'qs';

var CTM = {
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

    getAllPhoneCodeList: function(){

        return axios.get(window.apiDomainUrl+'/countries/get-all-phone-code-list', qs.stringify({}))

    },
};

export function CountriesManager() {
    return CTM;
}