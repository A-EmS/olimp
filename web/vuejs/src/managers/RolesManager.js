import axios from 'axios';
import qs from 'qs';

var RLS = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/roles/get-all', qs.stringify({}))
    },

    getForSelect: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/roles/get-all-for-select', qs.stringify(createData));
    },

    getAllForUserForm: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/roles/get-all-for-user-form', qs.stringify(createData));
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/roles/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/roles/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/roles/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/roles/delete', qs.stringify(data));
    },

    getActionAccess: function(accessName){

        return axios.get(window.apiDomainUrl+'/roles/get-action-access?accessName='+accessName, qs.stringify({}));
    },

    getAccessList: function(accessLabelId){

        return axios.get(window.apiDomainUrl+'/roles/get-access-list?accessLabelId='+accessLabelId, qs.stringify({}));
    },

};

export function RolesManager() {
    return RLS;
}