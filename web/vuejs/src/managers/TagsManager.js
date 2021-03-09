import axios from 'axios';
import qs from 'qs';

var TGS = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/tags/get-all', qs.stringify({}))
    },

    getForSelect: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/tags/get-all-for-select', qs.stringify(createData));
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/tags/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/tags/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/tags/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/tags/delete', qs.stringify(data));
    },

};

export function TagsManager() {
    return TGS;
}