import axios from 'axios';
import qs from 'qs';

var PHM = {

    getAllByProjectId: function(id){

        return axios.get(window.apiDomainUrl+'/project-history/get-all-by-project-id?projectId='+id, qs.stringify({}));
    },

};

export function ProjectHistoryManager() {
    return PHM;
}