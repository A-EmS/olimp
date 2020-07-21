import axios from 'axios';
import qs from 'qs';

var Contacts = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/contacts/get-all-for-select', qs.stringify(createData));
    },

    findByName: function (value) {

        let createData = {

        };

        return axios.get(window.apiDomainUrl+'/contacts/find-by-name?name='+value, qs.stringify(createData));
    },

    findForHeaderSearch: function (id) {

        let createData = {

        };

        return axios.get(window.apiDomainUrl+'/contacts/find-for-header-search?id='+id, qs.stringify(createData));
    },

    findByContractorId: function (contractorId) {

        let createData = {

        };

        return axios.get(window.apiDomainUrl+'/contacts/find-by-contractor-id?id='+contractorId, qs.stringify(createData));
    }
};

export function ContactsManager() {
    return Contacts;
}