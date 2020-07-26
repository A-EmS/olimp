import axios from 'axios';
import qs from 'qs';

var FCM = {
    getAll: function(id){
        var id = id || 0;
        return axios.get(window.apiDomainUrl+'/finance-classes/get-all?id='+id, qs.stringify({}))
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/finance-classes/get-by-id?id='+id, qs.stringify({}));
    },

    getChildrenByNodeId: function(id){

        return axios.get(window.apiDomainUrl+'/finance-classes/get-children-by-node-id?id='+id, qs.stringify({}));
    },

    getInitNodes: function(){

        return axios.get(window.apiDomainUrl+'/finance-classes/get-init-nodes', qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/finance-classes/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/finance-classes/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/finance-classes/delete', qs.stringify(data));
    },

    move: function(data){

        return axios.post(window.apiDomainUrl+'/finance-classes/move', qs.stringify(data));
    },
};

export function FinanceClassesManager() {
    return FCM;
}