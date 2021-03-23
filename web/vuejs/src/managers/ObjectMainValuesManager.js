import axios from 'axios';
import qs from 'qs';

var OMVM = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/object-main-values/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/object-main-values/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/object-main-values/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/object-main-values/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/object-main-values/delete', qs.stringify(data));
    },
};

export function ObjectMainValuesManager() {
    return OMVM;
}