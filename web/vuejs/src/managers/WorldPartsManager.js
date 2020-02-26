import axios from 'axios';
import qs from 'qs';

var WorldParts = {
    getForSelect: function(string){

        return axios.get(window.apiDomainUrl+'/world-parts/get-all-for-countries', qs.stringify({}))
    },
};

export function WorldPartsManager() {
    return WorldParts;
}