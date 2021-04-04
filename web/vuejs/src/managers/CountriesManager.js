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

    getForSelectAccordingProjectParts: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select-according-project-parts', qs.stringify({}))

    },

    getForSelectAccordingDocumentTypes: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select-according-document-types', qs.stringify({}))

    },

    getForSelectAccordingRequests: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-for-select-according-requests', qs.stringify({}))

    },

    getAllPhoneCodeList: function(){

        return axios.get(window.apiDomainUrl+'/countries/get-all-phone-code-list', qs.stringify({}))

    },

    getByContractorId: function (contractorId) {

        return axios.get(window.apiDomainUrl+'/countries/get-by-contractor-id?contractorId='+contractorId, qs.stringify({}));
    }
};

export function CountriesManager() {
    return CTM;
}