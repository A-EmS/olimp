import axios from 'axios';
import qs from 'qs';

var TXS = {

    getAll: function(){

        return axios.get(window.apiDomainUrl+'/taxes/get-all', qs.stringify({}))
    },

    getForDocumentContent: function(documentId){

        return axios.get(window.apiDomainUrl+'/taxes/get-for-document-content?documentId='+documentId, qs.stringify({}))
    },
};

export function TaxesManager() {
    return TXS;
}