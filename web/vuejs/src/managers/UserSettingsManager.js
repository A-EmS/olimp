import axios from 'axios';
import qs from 'qs';

var UserSettings = {

    getAllByUserId: function(userId){

        return axios.get(window.apiDomainUrl+'/user-settings/get-all-by-user-id?userId='+userId, qs.stringify({}))
    },

    change: function(objectData){

        return axios.post(window.apiDomainUrl+'/user-settings/change', qs.stringify({data:objectData}))
    },
};

export function UserSettingsManager() {
    return UserSettings;
}