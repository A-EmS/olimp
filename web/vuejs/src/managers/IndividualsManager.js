import axios from 'axios';
import qs from 'qs';

var IndividualsManager = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/individuals/get-all-for-select', qs.stringify(createData));
    },

    getAllByTerm: function(term){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/individuals/get-all-by-term?term='+term, qs.stringify(createData));
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/individuals/get-by-id?id='+id, qs.stringify({}))
    },

};

export function IM() {
    return IndividualsManager;
}