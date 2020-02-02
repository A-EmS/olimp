import axios from 'axios';
import qs from 'qs';

var ContractorsManager = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/contractors/get-all-for-select', qs.stringify(createData));
    },
};

export function CM() {
    return ContractorsManager;
}