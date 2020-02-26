import axios from 'axios';
import qs from 'qs';

var ContactTypes = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/contact-types/get-all-for-select', qs.stringify(createData));
    },
};

export function ContactTypesManager() {
    return ContactTypes;
}