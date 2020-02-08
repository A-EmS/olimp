import axios from 'axios';
import qs from 'qs';

var PersonalManager = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/personal/get-all-for-select', qs.stringify(createData));
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/personal/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/personal/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/personal/update', qs.stringify(data));
    },
};

export function PM() {
    return PersonalManager;
}