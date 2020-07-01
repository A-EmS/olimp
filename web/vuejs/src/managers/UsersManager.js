import axios from 'axios';
import qs from 'qs';

var USR = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/user/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/user/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/user/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/user/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/user/delete', qs.stringify(data));
    },
};

export function UsersManager() {
    return USR;
}