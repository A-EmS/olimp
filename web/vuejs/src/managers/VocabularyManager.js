import axios from 'axios';
import qs from 'qs';

var VocabularyManager = {
    create: function(string){

        var createData = {
            lang_en: string,
        };

        return axios.post(window.apiDomainUrl+'/interface-vocabularies/create', qs.stringify(createData));
    },
};

export function VM() {
    return VocabularyManager;
}