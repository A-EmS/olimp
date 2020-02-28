import axios from 'axios';
import qs from 'qs';

var RM = {

    getForSelectByCountry: function(countryId){

        return axios.get(window.apiDomainUrl+'/regions/get-all-by-country?countryId='+countryId, qs.stringify({}))
    },
};

export function RegionsManager() {
    return RM;
}