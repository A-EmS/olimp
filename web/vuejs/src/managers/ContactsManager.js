import axios from 'axios';
import qs from 'qs';

var Contacts = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/contacts/get-all-for-select', qs.stringify(createData));
    },
};

export function ContactsManager() {
    return Contacts;
}