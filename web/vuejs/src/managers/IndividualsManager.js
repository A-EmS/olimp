import axios from 'axios';
import qs from 'qs';

var IndividualsManager = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/individuals/get-all-for-select', qs.stringify(createData));
    },
};

export function IM() {
    return IndividualsManager;
}