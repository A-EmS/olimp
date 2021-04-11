import axios from 'axios';
import qs from 'qs';

var DGM = {
    generate: function(data){

        return axios.post(window.apiDomainUrl+'/document-generator/generate', qs.stringify(data));
    },
    download: function (id, document_type) {
        window.open(window.apiDomainUrl+'/document-generator/download?id='+id+'&documentTypeId='+document_type);
    }

};

export function DocumentGeneratorManager() {
    return DGM;
}