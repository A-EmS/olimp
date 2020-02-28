import axios from 'axios';
import qs from 'qs';

var EntityTypes = {

    getEntityTypesByCountryId: function(countryId){

        return axios.get(window.apiDomainUrl+'/entity-types/get-all-for-select-by-country?countryId='+countryId, qs.stringify({}))
    },
};

export function EntityTypesManager() {
    return EntityTypes;
}