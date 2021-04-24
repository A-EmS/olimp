import axios from 'axios';
import qs from 'qs';

var RQM = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/requests/get-all', qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/requests/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/requests/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/requests/update', qs.stringify(data));
    },

    delete: function(data){
        return axios.post(window.apiDomainUrl+'/requests/delete', qs.stringify(data));
    },

    copy: function(data){
        return axios.post(window.apiDomainUrl+'/requests/copy', qs.stringify(data));
    },

    // getAllRequests: function(){
    //
    //     return axios.get(window.apiDomainUrl+'/requests/get-all-requests', qs.stringify({}))
    // },

    getRequestsByPage: function(page, perPage, filters){

        return axios.post(window.apiDomainUrl+'/requests/get-all-requests-by-page', qs.stringify({page:page, perPage:perPage, filters:filters}))
    },
};

export function RequestsManager() {
    return RQM;
}