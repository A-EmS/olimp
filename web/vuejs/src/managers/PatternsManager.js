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
        return axios.post(
            window.apiDomainUrl+'/patterns/create',
            data,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );
    },

    update: function(data){

        return axios.post(
            window.apiDomainUrl+'/patterns/update',
            data,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );
    },

    delete: function(data){

        return axios.post(window.apiDomainUrl+'/patterns/delete', qs.stringify(data));
    },

    download: function (id) {
        window.open(window.apiDomainUrl+'/patterns/download?id='+id);
    }

};

export function PatternsManager() {
    return PTS;
}