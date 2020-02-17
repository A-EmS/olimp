import axios from 'axios';
import qs from 'qs';

var CTM = {
    getForSelectAccordingEntityTypes: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/countries/get-all-for-select-according-entity-types', qs.stringify({}))

    },
};

export function CountriesManager() {
    return CTM;
}