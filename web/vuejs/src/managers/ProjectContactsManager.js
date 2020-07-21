import axios from 'axios';
import qs from 'qs';

var PCM = {

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/project-contacts/get-by-id?id='+id, qs.stringify({}));
    },

    getAllByProjectId: function(id){

        return axios.get(window.apiDomainUrl+'/project-contacts/get-all-by-project-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/project-contacts/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/project-contacts/update', qs.stringify(data));
    },

    delete: function(data){
        
        return axios.post(window.apiDomainUrl+'/project-contacts/delete', qs.stringify(data));
    },
};

export function ProjectContactsManager() {
    return PCM;
}