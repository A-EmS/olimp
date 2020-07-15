import axios from 'axios';
import qs from 'qs';

var CLD = {

    getByYearAndCountry: function(data){
        return axios.get(window.apiDomainUrl+'/calendar/get-by-year-and-country?year='+data.year+'&countryId='+data.countryId, qs.stringify({}));
    },

    getCountryList: function(){
        return axios.post(window.apiDomainUrl+'/calendar/get-country-list', qs.stringify());
    },

    update: function(data){
        return axios.post(window.apiDomainUrl+'/calendar/update', qs.stringify(data));
    },

    generateForCountry: function(data){
        return axios.post(window.apiDomainUrl+'/calendar/generate-for-country', qs.stringify(data));
    },
};

export function CalendarManager() {
    return CLD;
}