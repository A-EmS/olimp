export default {
    methods: {
        onlyDigits: function(e){
            var pattern = /^[0-9]+$/i;
            if (![37,39,36,35,8,46].includes(e.which) && !pattern.test(e.key)){
                e.preventDefault();
                e.stopPropagation();
            }
        },

        emailValidation: function(email){
            const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            return pattern.test(email);
        },
    }
}

