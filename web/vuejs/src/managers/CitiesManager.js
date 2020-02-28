import axios from 'axios';
import qs from 'qs';

var CT = {
    getForSelect: function(string){

        var createData = {

        };
        return axios.get(window.apiDomainUrl+'/cities/get-all-for-select', qs.stringify({}))
    },

    getForSelectByRegion: function(regionId){

        return axios.get(window.apiDomainUrl+'/cities/get-all-by-region?regionId='+regionId, qs.stringify({}))
    },
};

export function CitiesManager() {
    return CT;
}