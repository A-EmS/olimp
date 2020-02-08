import axios from 'axios';
import qs from 'qs';

var EntitiesManager = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/entities/get-all-for-select', qs.stringify(createData));
    },
};

export function EM() {
    return EntitiesManager;
}