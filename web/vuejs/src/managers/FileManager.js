import axios from 'axios';
import qs from 'qs';

var FM = {

    uploadFile: function(formData) {
        return axios.post( window.apiDomainUrl+'/file/upload',
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );
    },
};

export function FileManager() {
    return FM;
}
