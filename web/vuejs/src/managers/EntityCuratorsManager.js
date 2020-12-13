import axios from 'axios';
import qs from 'qs';

var EntityCurators = {

    getAllByEntityId: function(entityId){

        return axios.get(window.apiDomainUrl+'/entity-curators/get-all-by-entity-id?entityId='+entityId, qs.stringify({}))
    },

    create: function(createData){

        return axios.post(window.apiDomainUrl+'/entity-curators/create', qs.stringify(createData))
    },

    deleteRow: function(userOwnCompanyId){

        return axios.post(window.apiDomainUrl+'/entity-curators/delete', qs.stringify({id:userOwnCompanyId}))
    },
};

export function EntityCuratorsManager() {
    return EntityCurators;
}