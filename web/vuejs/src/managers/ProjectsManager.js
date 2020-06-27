import axios from 'axios';
import qs from 'qs';

var PRM = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/projects/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/projects/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/projects/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/projects/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/projects/delete', qs.stringify(data));
    },
};

export function ProjectsManager() {
    return PRM;
}