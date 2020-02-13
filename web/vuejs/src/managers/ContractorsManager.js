import axios from 'axios';
import qs from 'qs';

var ContractorsManager = {
    getForSelect: function(string){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/contractors/get-all-for-select', qs.stringify(createData));
    },

    getContractorByRefIdAndType: function(data){

        var createData = {

        };

        return axios.get(window.apiDomainUrl+'/contractors/get-by-ref-id-and-type?refId='+data.ref_id+'&isEntity='+data.is_entity, qs.stringify(createData));
    },
};

export function CM() {
    return ContractorsManager;
}