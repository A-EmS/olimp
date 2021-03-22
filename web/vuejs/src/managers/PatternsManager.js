import axios from 'axios';
import qs from 'qs';

var PTS = {
    getAll: function(){

        return axios.get(window.apiDomainUrl+'/patterns/get-all', qs.stringify({}))
    },

    getForSelect: function(){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/patterns/get-all-for-select', qs.stringify(createData));
    },

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/patterns/get-by-id?id='+id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/patterns/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/patterns/update', qs.stringify(data));
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/patterns/delete', qs.stringify(data));
    },

};

export function PatternsManager() {
    return PTS;
}