var validationMixin = {
    created: function () {

    },
    methods: {
        floatValidation: function(evt, model) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            var pointIsPresented = model.toString().indexOf('.') !== -1;
            var stringIsEmpty = model === '';
            if (
                (
                    (charCode > 31 && (charCode < 48 || charCode > 57))
                    && charCode !== 46
                )
                || (charCode === 46 && (pointIsPresented || stringIsEmpty))) {
                evt.preventDefault();
            } else {
                return true;
            }
        },

        intValidation: function(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57)) || charCode === 46) {
                evt.preventDefault();
            } else {
                return true;
            }
        }
    }
};
