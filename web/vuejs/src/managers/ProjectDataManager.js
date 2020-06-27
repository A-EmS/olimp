import axios from 'axios';
import qs from 'qs';

var PDM = {

    getById: function(id){

        return axios.get(window.apiDomainUrl+'/project-data/get-by-id?id='+id, qs.stringify({}));
    },

    getAllByProjectId: function(id){

        return axios.get(window.apiDomainUrl+'/project-data/get-all-by-project-id?id='+id, qs.stringify({}));
    },

    getRefreshedCrypt: function(project_id, project_part_id){

        return axios.get(window.apiDomainUrl+'/project-data/get-refreshed-crypt?project_id='+project_id+'&project_part_id='+project_part_id, qs.stringify({}));
    },

    create: function(data){

        return axios.post(window.apiDomainUrl+'/project-data/create', qs.stringify(data));
    },

    update: function(data){

        return axios.post(window.apiDomainUrl+'/project-data/update', qs.stringify(data));
    },

    delete: function(data){
        
        return axios.post(window.apiDomainUrl+'/project-data/delete', qs.stringify(data));
    },
};

export function ProjectDataManager() {
    return PDM;
}