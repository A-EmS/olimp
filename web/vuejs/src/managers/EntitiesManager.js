import axios from 'axios';
import qs from 'qs';

var EntitiesManager = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/entities/get-all-for-select', qs.stringify(createData));
    },

    getEntityTypesByCountryId: function(countryId){

        return axios.get(window.apiDomainUrl+'/entity-types/get-all-for-select-by-country?countryId='+countryId, qs.stringify({}))
    },
};

export function EM() {
    return EntitiesManager;
}